<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use Illuminate\Http\Request;

class KycController extends Controller
{
    /**
     * Show the current (approved) KYC record of the authenticated user, if any.
     */
    public function show(Request $request)
    {
        $kyc = Kyc::where('user_id',$request->user()->id)->first();
        if ($request->wantsJson()) {
            return response()->json([
                'data' => $kyc,
            ]);
        }
        return view('user.kyc.snapshot.show',[ 'kyc' => $kyc ]);
    }
}
