<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $departments = \App\Models\Department::all();
        return view('auth.register', compact('departments'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone'    => ['required', 'string', 'max:20', 'unique:'.User::class],
            'gender'   => ['nullable', 'string', 'in:male,female'],
            'dob'      => ['nullable', 'date'],
            'address'  => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'avatar'   => ['nullable', 'image', 'max:2048'],
        ]);

        // Handle avatar upload
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $latestId = User::max('id') + 1;
        $memberId = 'EAGT-KBP-' . str_pad((string)$latestId, 4, '0', STR_PAD_LEFT);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'gender'    => $request->gender,
            'dob'       => $request->dob,
            'address'   => $request->address,
            'member_id' => $memberId,
            'role'      => 'mshirika', // legacy
            'status'    => 'active',
            'avatar'    => $avatarPath,
            'password'  => Hash::make($request->password),
        ]);
        
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'mshiriki']);
        $user->assignRole('mshiriki');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false))->with('show_welcome_modal', true);
    }
}
