<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
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
        $query = Ticket::with(['user', 'assignedTo', 'comments'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by assigned admin
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Search by title, ticket number, or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('full_name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->paginate(15)->withQueryString();

        // Get admin users for assignment filter
        $admins = User::where('role', 'admin')->get();

        return view('admin.tickets.index', compact('tickets', 'admins'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket): View
    {
        $ticket->load(['user', 'assignedTo', 'comments.user']);
        
        // Get admin users for assignment
        $admins = User::where('role', 'admin')->get();

        return view('admin.tickets.show', compact('ticket', 'admins'));
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $updateData = [
            'status' => $validated['status']
        ];

        if ($validated['status'] === 'resolved') {
            $updateData['resolved_at'] = now();
        }

        $ticket->update($updateData);

        // Add admin comment if provided
        if (!empty($validated['admin_notes'])) {
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'comment' => $validated['admin_notes'],
                'is_internal' => false
            ]);
        }

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Status ticket berhasil diperbarui.');
    }

    /**
     * Assign ticket to admin
     */
    public function assign(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        $ticket->update([
            'assigned_to' => $validated['assigned_to']
        ]);

        $assignedAdmin = User::find($validated['assigned_to']);
        $message = $assignedAdmin 
            ? "Ticket berhasil ditugaskan kepada {$assignedAdmin->full_name}."
            : "Penugasan ticket berhasil dihapus.";

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', $message);
    }

    /**
     * Add comment/reply to ticket
     */
    public function addComment(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'is_internal' => 'boolean',
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
            'is_internal' => $validated['is_internal'] ?? false
        ]);

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Balasan berhasil ditambahkan.');
    }

    /**
     * Bulk update tickets
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tickets' => 'required|array',
            'tickets.*' => 'exists:tickets,id',
            'action' => 'required|in:assign,status,priority',
            'assigned_to' => 'nullable|exists:users,id|required_if:action,assign',
            'status' => 'nullable|in:open,in_progress,resolved,closed|required_if:action,status',
            'priority' => 'nullable|in:low,medium,high,urgent|required_if:action,priority'
        ]);

        $updateData = [];
        
        switch ($validated['action']) {
            case 'assign':
                $updateData['assigned_to'] = $validated['assigned_to'];
                break;
            case 'status':
                $updateData['status'] = $validated['status'];
                if ($validated['status'] === 'resolved') {
                    $updateData['resolved_at'] = now();
                }
                break;
            case 'priority':
                $updateData['priority'] = $validated['priority'];
                break;
        }

        Ticket::whereIn('id', $validated['tickets'])->update($updateData);

        $count = count($validated['tickets']);
        return redirect()->route('admin.tickets.index')
            ->with('success', "{$count} ticket berhasil diperbarui.");
    }

    /**
     * Download attachment
     */
    public function downloadAttachment(Ticket $ticket, $type, $index)
    {
        if ($type === 'ticket') {
            $attachments = $ticket->attachments;
        } else {
            // For comment attachments
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
    
    /**
     * Get count of open tickets for admin sidebar
     */
    public function getCount()
    {
        $count = Ticket::whereIn('status', ['open', 'pending', 'in_progress'])->count();
        return response()->json(['count' => $count]);
    }
}
