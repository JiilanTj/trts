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
     * Unified edit profile page (user + detail)
     */
    public function editCombined(Request $request)
    {
        $user = $request->user()->load('detail');
        return view('user.profile.edit', compact('user'));
    }

    /**
     * Update user main + detail in one request
     */
    public function updateCombined(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'full_name' => 'nullable|string|max:150',
            'username' => 'nullable|string|max:150|unique:users,username,' . $user->id,
            'photo' => 'nullable|image|max:2048',
            // detail fields (slim)
            'phone' => 'nullable|string|max:30',
            'secondary_phone' => 'nullable|string|max:30',
            'address_line' => 'nullable|string|max:255',
        ]);

        // Update user main
        $user->fill(array_filter([
            'full_name' => $validated['full_name'] ?? $user->full_name,
            'username' => $validated['username'] ?? $user->username,
        ], fn($v) => !is_null($v)));

        // Handle photo upload
        if($request->hasFile('photo')){
            $path = $request->file('photo')->store('profiles','public');
            $user->photo = basename($path);
        }
        $user->save();

        // Update or create detail
        $detailData = collect($validated)->only([
            'phone','secondary_phone','address_line'
        ])->toArray();
        UserDetail::updateOrCreate(['user_id'=>$user->id], $detailData);

        return redirect()->route('user.profile.edit')->with('success','Profil berhasil diperbarui');
    }

    /**
     * Create or update (upsert) current user's detail (legacy JSON endpoint)
     */
    public function upsert(Request $request)
    {
        $data = $request->validate([
            'phone' => 'nullable|string|max:30',
            'secondary_phone' => 'nullable|string|max:30',
            'address_line' => 'nullable|string|max:255',
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
