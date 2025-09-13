<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TopupRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TopupController extends Controller
{
    public function index(Request $request)
    {
        $query = TopupRequest::with(['user', 'approvedBy', 'rejectedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $topupRequests = $query->paginate(15);

        return view('admin.topup.index', compact('topupRequests'));
    }

    public function show(TopupRequest $topupRequest)
    {
        $topupRequest->load(['user', 'approvedBy', 'rejectedBy']);
        
        return view('admin.topup.show', compact('topupRequest'));
    }

    public function approve(TopupRequest $topupRequest, Request $request)
    {
        if (!$topupRequest->isPending()) {
            return redirect()->back()
                ->with('error', 'Hanya permintaan topup dengan status pending yang bisa disetujui.');
        }

        $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($topupRequest, $request) {
            // Update topup request status
            $topupRequest->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            // Add balance to user
            $topupRequest->user->addBalance((int) $topupRequest->amount);

            // Track transaction amount and check level upgrade
            $topupRequest->user->addTransactionAmount((int) $topupRequest->amount);

            // Create notification for user
            Notification::create([
                'for_user_id' => $topupRequest->user_id,
                'category' => 'topup',
                'title' => 'Topup Disetujui',
                'description' => 'Topup saldo sebesar Rp ' . number_format((float) $topupRequest->amount, 0, ',', '.') . ' telah disetujui dan ditambahkan ke saldo Anda.',
            ]);
        });

        return redirect()->route('admin.topup.index')
            ->with('success', 'Permintaan topup berhasil disetujui dan saldo user telah ditambahkan.');
    }

    public function reject(TopupRequest $topupRequest, Request $request)
    {
        if (!$topupRequest->isPending()) {
            return redirect()->back()
                ->with('error', 'Hanya permintaan topup dengan status pending yang bisa ditolak.');
        }

        $request->validate([
            'admin_notes' => ['required', 'string', 'max:1000'],
        ], [
            'admin_notes.required' => 'Alasan penolakan wajib diisi.',
        ]);

        DB::transaction(function () use ($topupRequest, $request) {
            // Update topup request status
            $topupRequest->update([
                'status' => 'rejected',
                'rejected_by' => Auth::id(),
                'rejected_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            // Create notification for user
            Notification::create([
                'for_user_id' => $topupRequest->user_id,
                'category' => 'topup',
                'title' => 'Topup Ditolak',
                'description' => 'Topup saldo sebesar Rp ' . number_format((float) $topupRequest->amount, 0, ',', '.') . ' ditolak. Alasan: ' . $request->admin_notes,
            ]);
        });

        return redirect()->route('admin.topup.index')
            ->with('success', 'Permintaan topup berhasil ditolak.');
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'topup_ids' => ['required', 'array'],
            'topup_ids.*' => ['exists:topup_requests,id'],
            'admin_notes' => ['required_if:action,reject', 'string', 'max:1000'],
        ]);

        $topupRequests = TopupRequest::whereIn('id', $request->topup_ids)
            ->pending()
            ->get();

        if ($topupRequests->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada permintaan topup yang valid untuk diproses.');
        }

        DB::transaction(function () use ($topupRequests, $request) {
            foreach ($topupRequests as $topupRequest) {
                if ($request->action === 'approve') {
                    $this->processApproval($topupRequest, $request->admin_notes);
                } else {
                    $this->processRejection($topupRequest, $request->admin_notes);
                }
            }
        });

        $message = $request->action === 'approve' 
            ? 'Permintaan topup berhasil disetujui secara massal.'
            : 'Permintaan topup berhasil ditolak secara massal.';

        return redirect()->route('admin.topup.index')
            ->with('success', $message);
    }

    private function processApproval(TopupRequest $topupRequest, ?string $adminNotes)
    {
        $topupRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_notes' => $adminNotes,
        ]);

        $topupRequest->user->addBalance((int) $topupRequest->amount);

        // Track transaction amount and check level upgrade
        $topupRequest->user->addTransactionAmount((int) $topupRequest->amount);

        Notification::create([
            'for_user_id' => $topupRequest->user_id,
            'category' => 'topup',
            'title' => 'Topup Disetujui',
            'description' => 'Topup saldo sebesar Rp ' . number_format((float) $topupRequest->amount, 0, ',', '.') . ' telah disetujui dan ditambahkan ke saldo Anda.',
        ]);
    }

    private function processRejection(TopupRequest $topupRequest, string $adminNotes)
    {
        $topupRequest->update([
            'status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'admin_notes' => $adminNotes,
        ]);

        Notification::create([
            'for_user_id' => $topupRequest->user_id,
            'category' => 'topup',
            'title' => 'Topup Ditolak',
            'description' => 'Topup saldo sebesar Rp ' . number_format((float) $topupRequest->amount, 0, ',', '.') . ' ditolak. Alasan: ' . $adminNotes,
        ]);
    }
}
