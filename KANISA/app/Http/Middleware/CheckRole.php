<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        if ($role === 'admin' && !$request->user()->isAdmin()) {
            abort(403, 'Hauruhusiwi kuingia hapa. Ni kwa ajili ya System Admin (Super Admin) pekee.');
        }

        if ($role === 'mchungaji' && !$request->user()->isMchungaji() && !$request->user()->isAdmin()) {
            abort(403, 'Hauruhusiwi kuingia hapa. Ni kwa ajili ya Mchungaji au Admin pekee.');
        }

        if ($role === 'kiongozi' && !$request->user()->isKiongozi() && !$request->user()->isAdmin()) {
            abort(403, 'Hauruhusiwi kuingia hapa. Ni kwa ajili ya Viongozi pekee.');
        }

        return $next($request);
    }
}
