<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $departments = \App\Models\Department::all();
        return view('auth.login', compact('departments'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $login = trim((string) $request->input('login'));
        $provisionalMember = User::where('phone', $login)
            ->orWhereRaw('LOWER(email) = ?', [mb_strtolower($login)])
            ->orWhere('member_id', $login)
            ->first();

        // A moved-in visitor has a church record but not yet a personal account.
        // Send them to Sign up so their account updates that same record.
        if ($provisionalMember?->joined_as_visitor && Hash::check('password', $provisionalMember->password)) {
            return redirect()->route('register')->with('registration_hint', 'Ulisharekodiwa kama mhamia, lakini bado huna akaunti ya kuingia. Jisajili hapa kwa kutumia namba ileile ya simu ili taarifa zako ziunganishwe.');
        }

        $request->authenticate();

        $request->session()->regenerate();

        if (Auth::user()?->isAdmin()) {
            return redirect()->route('dashboard');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
