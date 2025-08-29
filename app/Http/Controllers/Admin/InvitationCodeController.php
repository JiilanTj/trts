<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationCode;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class InvitationCodeController extends Controller
{
    /**
     * Display a listing of invitation codes.
     */
    public function index(): View
    {
        $invitationCodes = InvitationCode::with('user')
            ->latest()
            ->paginate(15);

        return view('admin.invitation-codes.index', compact('invitationCodes'));
    }

    /**
     * Show the form for creating a new invitation code.
     */
    public function create(): View
    {
        return view('admin.invitation-codes.create');
    }

    /**
     * Store a newly created invitation code.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'max_usage' => 'required|integer|min:1|max:100',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $invitationCode = InvitationCode::create([
            'user_id' => Auth::id(),
            'code' => InvitationCode::generateUniqueCode(),
            'max_usage' => $request->max_usage,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.invitation-codes.index')
            ->with('success', 'Kode undangan berhasil dibuat: ' . $invitationCode->code);
    }

    /**
     * Display the specified invitation code.
     */
    public function show(InvitationCode $invitationCode): View
    {
        $invitationCode->load('user');
        
        return view('admin.invitation-codes.show', compact('invitationCode'));
    }

    /**
     * Update the specified invitation code status.
     */
    public function updateStatus(InvitationCode $invitationCode): RedirectResponse
    {
        $invitationCode->update([
            'is_active' => !$invitationCode->is_active
        ]);

        $status = $invitationCode->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
            ->with('success', "Kode undangan berhasil {$status}.");
    }

    /**
     * Remove the specified invitation code.
     */
    public function destroy(InvitationCode $invitationCode): RedirectResponse
    {
        $invitationCode->delete();

        return redirect()->route('admin.invitation-codes.index')
            ->with('success', 'Kode undangan berhasil dihapus.');
    }

    /**
     * Validate invitation code (for AJAX requests).
     */
    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $invitationCode = InvitationCode::where('code', $request->code)->first();

        if (!$invitationCode) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode undangan tidak ditemukan.'
            ]);
        }

        if (!$invitationCode->isValid()) {
            $message = 'Kode undangan tidak valid.';
            
            if (!$invitationCode->is_active) {
                $message = 'Kode undangan sudah tidak aktif.';
            } elseif ($invitationCode->used_count >= $invitationCode->max_usage) {
                $message = 'Kode undangan sudah mencapai batas penggunaan.';
            } elseif ($invitationCode->expires_at && $invitationCode->expires_at->isPast()) {
                $message = 'Kode undangan sudah kadaluarsa.';
            }

            return response()->json([
                'valid' => false,
                'message' => $message
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Kode undangan valid.',
            'data' => [
                'code' => $invitationCode->code,
                'max_usage' => $invitationCode->max_usage,
                'used_count' => $invitationCode->used_count,
                'remaining' => $invitationCode->max_usage - $invitationCode->used_count,
            ]
        ]);
    }
}
