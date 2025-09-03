<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserDetail;
use Illuminate\Http\Request;

class UserDetailController extends Controller
{
    /**
     * Get current user's detail (or empty structure)
     */
    public function show(Request $request)
    {
        $detail = UserDetail::where('user_id',$request->user()->id)->first();
        return response()->json([
            'data' => $detail,
        ]);
    }

    /**
     * Create or update (upsert) current user's detail
     */
    public function upsert(Request $request)
    {
        $data = $request->validate([
            'phone' => 'nullable|string|max:30',
            'secondary_phone' => 'nullable|string|max:30',
            'gender' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:120',
            'address_line' => 'nullable|string|max:255',
            'rt_rw' => 'nullable|string|max:30',
            'village' => 'nullable|string|max:120',
            'district' => 'nullable|string|max:120',
            'city' => 'nullable|string|max:120',
            'province' => 'nullable|string|max:120',
            'postal_code' => 'nullable|string|max:15',
            'nationality' => 'nullable|string|max:80',
            'marital_status' => 'nullable|string|max:40',
            'religion' => 'nullable|string|max:40',
            'occupation' => 'nullable|string|max:120',
            'extra' => 'nullable|array',
        ]);

        $detail = UserDetail::updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return response()->json([
            'message' => 'User detail disimpan',
            'data' => $detail,
        ]);
    }
}
