<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycRequest;
use App\Models\Kyc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KycRequestController extends Controller
{
    /** List all KYC requests with optional status filter */
    public function index(Request $request)
    {
        $query = KycRequest::query()->with('user');
        if ($status = $request->get('status')) {
            $query->where('status_kyc',$status);
        }
        $requests = $query->latest()->paginate(30);
        return response()->json(['data'=>$requests]);
    }

    /** Show single request */
    public function show(KycRequest $kycRequest)
    {
        $kycRequest->load('user','reviewer','kyc');
        return response()->json(['data'=>$kycRequest]);
    }

    /** Move request to review */
    public function startReview(Request $request, KycRequest $kycRequest)
    {
        if ($kycRequest->status_kyc !== KycRequest::STATUS_PENDING) {
            return response()->json(['message'=>'Status tidak valid untuk mulai review'],422);
        }
        $kycRequest->update([
            'status_kyc' => KycRequest::STATUS_REVIEW,
            'reviewed_at' => now(),
            'reviewed_by' => $request->user()->id,
        ]);
        return response()->json(['message'=>'Masuk review','data'=>$kycRequest]);
    }

    /** Approve request and create final KYC snapshot */
    public function approve(Request $request, KycRequest $kycRequest)
    {
        if (!in_array($kycRequest->status_kyc,[KycRequest::STATUS_PENDING,KycRequest::STATUS_REVIEW])) {
            return response()->json(['message'=>'Tidak bisa approve dari status ini'],422);
        }

        DB::transaction(function() use ($kycRequest,$request){
            $kycRequest->update([
                'status_kyc' => KycRequest::STATUS_APPROVED,
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
            ]);

            // Create / replace final KYC record (one per user)
            Kyc::updateOrCreate(
                ['user_id' => $kycRequest->user_id],
                [
                    'kyc_request_id' => $kycRequest->id,
                    'full_name' => $kycRequest->full_name,
                    'nik' => $kycRequest->nik,
                    'birth_place' => $kycRequest->birth_place,
                    'birth_date' => $kycRequest->birth_date,
                    'address' => $kycRequest->address,
                    'rt_rw' => $kycRequest->rt_rw,
                    'village' => $kycRequest->village,
                    'district' => $kycRequest->district,
                    'religion' => $kycRequest->religion,
                    'marital_status' => $kycRequest->marital_status,
                    'occupation' => $kycRequest->occupation,
                    'nationality' => $kycRequest->nationality,
                    'ktp_front_path' => $kycRequest->ktp_front_path,
                    'ktp_back_path' => $kycRequest->ktp_back_path,
                    'selfie_ktp_path' => $kycRequest->selfie_ktp_path,
                    'verified_at' => now(),
                    'verified_by' => $request->user()->id,
                ]
            );
        });

        return response()->json(['message'=>'Disetujui']);
    }

    /** Reject request */
    public function reject(Request $request, KycRequest $kycRequest)
    {
        if (!in_array($kycRequest->status_kyc,[KycRequest::STATUS_PENDING,KycRequest::STATUS_REVIEW])) {
            return response()->json(['message'=>'Tidak bisa reject dari status ini'],422);
        }
        $data = $request->validate([
            'admin_notes' => 'nullable|string'
        ]);
        $kycRequest->update([
            'status_kyc' => KycRequest::STATUS_REJECTED,
            'admin_notes' => $data['admin_notes'] ?? null,
            'reviewed_at' => now(),
            'reviewed_by' => $request->user()->id,
        ]);
        return response()->json(['message'=>'Ditolak','data'=>$kycRequest]);
    }
}
