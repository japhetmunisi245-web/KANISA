<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $departmentIds = $data['department_ids'] ?? [];
        $hasDepartmentSelection = $request->has('department_ids');
        unset($data['department_ids'], $data['avatar']);
        if (($data['marital_status'] ?? null) !== 'married') {
            $data['spouse_names'] = null;
        }

        $request->user()->fill($data);

        if ($request->hasFile('avatar')) {
            $request->user()->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        if ($hasDepartmentSelection) {
            $existingDepartments = $request->user()->departments()->get()->keyBy('id');
            $syncData = collect($departmentIds)->mapWithKeys(function ($departmentId) use ($existingDepartments) {
                $existing = $existingDepartments->get($departmentId);

                return [$departmentId => $existing
                    ? ['role' => $existing->pivot->role, 'cheo' => $existing->pivot->cheo]
                    : ['role' => 'mshirika']];
            })->all();

            $request->user()->departments()->sync($syncData);
        }

        if ($request->boolean('return_to_dashboard')) {
            return Redirect::route('dashboard')->with('success', 'Taarifa zako zimehifadhiwa.');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
