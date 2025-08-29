<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display the settings (single row scenario).
     */
    public function index()
    {
        $setting = Setting::first();
        return view('admin.settings.index', compact('setting'));
    }

    /**
     * Update the settings row (id=1 assumption or first row).
     */
    public function update(Request $request)
    {
        $setting = Setting::firstOrCreate(['id' => 1]);

        $validated = $request->validate([
            'payment_provider' => ['nullable','string','max:100'],
            'account_name' => ['nullable','string','max:150'],
            'account_number' => ['nullable','string','max:100'],
            'logo' => ['nullable','image','max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($setting->logo && Storage::disk('public')->exists($setting->logo)) {
                Storage::disk('public')->delete($setting->logo);
            }
            $validated['logo'] = $request->file('logo')->store('settings','public');
        }

        $setting->update($validated);

        return redirect()->route('admin.settings.index')->with('success','Pengaturan berhasil diperbarui.');
    }
}
