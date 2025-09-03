<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use Illuminate\Http\Request;

class KycController extends Controller
{
    /**
     * List all finalized KYC records (approved ones)
     */
    public function index(Request $request)
    {
        $query = Kyc::query()->with('user');
        if ($userId = $request->get('user_id')) {
            $query->where('user_id',$userId);
        }
        $kycs = $query->latest()->paginate(30)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json(['data'=>$kycs]);
        }

        return view('admin.kyc.snapshots.index', [
            'kycs' => $kycs,
        ]);
    }

    /**
     * Show single KYC snapshot
     */
    public function show(Request $request, Kyc $kyc)
    {
        $kyc->load('user','request','verifier');
        if ($request->wantsJson()) {
            return response()->json(['data'=>$kyc]);
        }
        return view('admin.kyc.snapshots.show', [
            'kyc' => $kyc,
        ]);
    }
}
