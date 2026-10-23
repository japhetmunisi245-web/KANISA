<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'phone'    => ['required', 'string', 'max:20'],
            'gender'   => ['nullable', 'string', 'in:male,female'],
            'dob'      => ['nullable', 'date'],
            'address'  => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'avatar'   => ['nullable', 'image', 'max:2048'],
        ]);

        $email = mb_strtolower(trim($request->email));
        $phone = trim($request->phone);
        $movedMembers = User::where('joined_as_visitor', true)->get();
        $movedMember = $movedMembers->first(fn (User $user) => $this->normalisedPhone($user->phone) === $this->normalisedPhone($phone))
            ?? $movedMembers->first(fn (User $user) => ! str_ends_with((string) $user->email, '@kanisa.local') && mb_strtolower((string) $user->email) === $email);

        $emailOwner = User::whereRaw('LOWER(email) = ?', [$email])->first();
        $phoneOwner = User::where('phone', $phone)->first();

        if ($emailOwner && (! $movedMember || $emailOwner->id !== $movedMember->id)) {
            throw ValidationException::withMessages(['email' => 'Barua pepe hii tayari imetumika.']);
        }
        if ($phoneOwner && (! $movedMember || $phoneOwner->id !== $movedMember->id)) {
            throw ValidationException::withMessages(['phone' => 'Namba hii ya simu tayari imesajiliwa.']);
        }

        // Handle avatar upload.
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = DB::transaction(function () use ($request, $email, $phone, $avatarPath, $movedMember) {
            $details = [
                'name' => $request->name, 'email' => $email, 'phone' => $phone,
                'gender' => $request->gender, 'dob' => $request->dob,
                'address' => $request->address, 'password' => Hash::make($request->password),
            ];

            if ($movedMember) {
                // This is the same person recorded by the secretary as a moved-in visitor.
                // Keep their transfer history, letter, member number and attendance links.
                $movedMember->update($details + array_filter(['avatar' => $avatarPath]));
                return $movedMember;
            }

            $latestId = User::max('id') + 1;
            return User::create($details + [
                'member_id' => 'EAGT-KBP-' . str_pad((string) $latestId, 4, '0', STR_PAD_LEFT),
                'role' => 'mshirika', 'status' => 'active', 'avatar' => $avatarPath,
            ]);
        });
        
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'mshiriki']);
        $user->assignRole('mshiriki');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false))
            ->with('show_welcome_modal', true)
            ->with('success', $movedMember ? 'Usajili umekamilika. Taarifa zako za uhamisho zimeunganishwa na akaunti hii.' : 'Usajili umekamilika.');
    }

    private function normalisedPhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return '255'.substr($digits, 1);
        }

        return $digits;
    }
}
