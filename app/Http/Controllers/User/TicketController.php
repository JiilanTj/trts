<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Ticket::where('user_id', Auth::id())
            ->with(['assignedTo', 'comments'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search by title or ticket number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%");
            });
        }

        $tickets = $query->paginate(10)->withQueryString();

        return view('user.tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = [
            'technical' => 'Teknis',
            'billing' => 'Penagihan',
            'general' => 'Umum',
            'account' => 'Akun',
            'loan' => 'Pinjaman',
            'other' => 'Lainnya'
        ];

        $priorities = [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'urgent' => 'Mendesak'
        ];

        return view('user.tickets.create', compact('categories', 'priorities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:technical,billing,general,account,loan,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'attachments.*' => 'nullable|file|max:2048|mimes:pdf,jpg,jpeg,png,doc,docx'
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ];
            }
        }

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'attachments' => $attachments,
            'status' => 'open'
        ]);

        return redirect()->route('user.tickets.show', $ticket)
            ->with('success', 'Ticket berhasil dibuat! Nomor ticket: ' . $ticket->ticket_number);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket): View
    {
        // Ensure user can only view their own tickets
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $ticket->load(['user', 'assignedTo', 'publicComments.user']);

        return view('user.tickets.show', compact('ticket'));
    }

    /**
     * Add comment to ticket
     */
    public function addComment(Request $request, Ticket $ticket): RedirectResponse
    {
        // Ensure user can only comment on their own tickets
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'comment' => 'required|string',
            'attachments.*' => 'nullable|file|max:2048|mimes:pdf,jpg,jpeg,png,doc,docx'
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/comments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ];
            }
        }

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
            'attachments' => $attachments,
            'is_internal' => false
        ]);

        return redirect()->route('user.tickets.show', $ticket)
            ->with('success', 'Komentar berhasil ditambahkan.');
    }

    /**
     * Download attachment
     */
    public function downloadAttachment(Ticket $ticket, $type, $index)
    {
        // Ensure user can only download from their own tickets
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        if ($type === 'ticket') {
            $attachments = $ticket->attachments;
        } else {
            // For comment attachments, we need to find the comment
            $comment = $ticket->comments()->findOrFail($index);
            $attachments = $comment->attachments;
            $index = 0; // Reset index for comment attachment
        }

        if (!isset($attachments[$index])) {
            abort(404);
        }

        $attachment = $attachments[$index];
        
        if (!Storage::disk('public')->exists($attachment['path'])) {
            abort(404);
        }

        return response()->download(storage_path('app/public/' . $attachment['path']), $attachment['name']);
    }
}
