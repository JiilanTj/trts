<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KycRequest;
use App\Models\Kyc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KycRequestController extends Controller
{
    /** List all KYC requests for the authenticated user */
    public function index(Request $request)
    {
        $requests = KycRequest::where('user_id', $request->user()->id)
            ->latest()->get();
        if ($request->wantsJson()) {
            return response()->json([
                'data' => $requests,
            ]);
        }
        return view('user.kyc.requests.index', [
            'requests' => $requests,
        ]);
    }

    /** Show single KYC request (JSON or Blade) */
    public function show(Request $request, KycRequest $kycRequest)
    {
        abort_unless($kycRequest->user_id === $request->user()->id, 403);
        if ($request->wantsJson()) {
            return response()->json(['data' => $kycRequest]);
        }
        return view('user.kyc.requests.show',[ 'kycRequest' => $kycRequest ]);
    }

    /** Create a new KYC request */
    public function store(Request $request)
    {
        $user = $request->user();
        // Prevent duplicate pending/review requests
        $exists = KycRequest::where('user_id',$user->id)
            ->whereIn('status_kyc',['pending','review'])
            ->exists();
        if ($exists) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Masih ada pengajuan KYC yang berjalan.'], 422);
            }
            return back()->withErrors(['full_name' => 'Masih ada pengajuan KYC yang berjalan.']);
        }

        $data = $request->validate([
            'full_name' => 'required|string|max:150',
            'nik' => 'nullable|string|max:32',
            'birth_place' => 'nullable|string|max:120',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'rt_rw' => 'nullable|string|max:30',
            'village' => 'nullable|string|max:120',
            'district' => 'nullable|string|max:120',
            'religion' => 'nullable|string|max:40',
            'marital_status' => 'nullable|string|max:40',
            'occupation' => 'nullable|string|max:120',
            'nationality' => 'nullable|string|max:50',
            'ktp_front' => 'required|file|mimes:jpg,jpeg,png,pdf',
            'ktp_back' => 'required|file|mimes:jpg,jpeg,png,pdf',
            'selfie_ktp' => 'required|file|mimes:jpg,jpeg,png,pdf',
        ]);

        $frontPath = $data['ktp_front']->store('kyc/front','public');
        $backPath = $data['ktp_back']->store('kyc/back','public');
        $selfiePath = $data['selfie_ktp']->store('kyc/selfie','public');

        $requestModel = KycRequest::create([
            'user_id' => $user->id,
            'full_name' => $data['full_name'],
            'nik' => $data['nik'] ?? null,
            'birth_place' => $data['birth_place'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'address' => $data['address'] ?? null,
            'rt_rw' => $data['rt_rw'] ?? null,
            'village' => $data['village'] ?? null,
            'district' => $data['district'] ?? null,
            'religion' => $data['religion'] ?? null,
            'marital_status' => $data['marital_status'] ?? null,
            'occupation' => $data['occupation'] ?? null,
            'nationality' => $data['nationality'] ?? null,
            'ktp_front_path' => $frontPath,
            'ktp_back_path' => $backPath,
            'selfie_ktp_path' => $selfiePath,
            'status_kyc' => KycRequest::STATUS_PENDING,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Pengajuan KYC dibuat','data'=>$requestModel], 201);
        }
        return redirect()->route('user.kyc.requests.show',$requestModel)->with('success','Pengajuan KYC berhasil dikirim');
    }
}
