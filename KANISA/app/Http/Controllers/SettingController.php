<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ChurchSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isMchungaji() && !$user->isAdmin()) {
            return redirect()->back()->with('error', 'Hauruhusiwi kubadilisha mipangilio ya Kanisa.');
        }

        $request->validate([
            'church_name'      => 'nullable|string|max:255',
            'address'          => 'nullable|string|max:255',
            'authorized_name'  => 'nullable|string|max:255',
            'authorized_title' => 'nullable|string|max:255',
            'stamp'            => 'nullable|image|mimes:png|max:2048', // Transparent PNG
            'signature'        => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'logo'             => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        if ($request->filled('church_name')) {
            ChurchSetting::updateOrCreate(['key' => 'church_name'], ['value' => $request->church_name]);
        }
        if ($request->filled('address')) {
            ChurchSetting::updateOrCreate(['key' => 'address'], ['value' => $request->address]);
        }
        if ($request->filled('authorized_name')) {
            ChurchSetting::updateOrCreate(['key' => 'authorized_name'], ['value' => $request->authorized_name]);
        }
        if ($request->filled('authorized_title')) {
            ChurchSetting::updateOrCreate(['key' => 'authorized_title'], ['value' => $request->authorized_title]);
        }

        // Stamp upload
        if ($request->hasFile('stamp')) {
            $path = $request->file('stamp')->store('stamps', 'public');
            ChurchSetting::updateOrCreate(['key' => 'stamp_path'], ['value' => $path]);
        }

        // Signature upload
        if ($request->hasFile('signature')) {
            $path = $request->file('signature')->store('signatures', 'public');
            ChurchSetting::updateOrCreate(['key' => 'signature_path'], ['value' => $path]);
        }

        // Logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            ChurchSetting::updateOrCreate(['key' => 'logo_path'], ['value' => $path]);
        }

        AuditLog::create([
            'user_id'     => $user->id,
            'action'      => 'SETTINGS_UPDATED',
            'description' => $user->name . ' amebadilisha Mipangilio ya Kanisa (Muhuri/Saini/Logo).',
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'Mipangilio ya Kanisa, Muhuri na Saini vimehifadhiwa kikamilifu!');
    }
}
