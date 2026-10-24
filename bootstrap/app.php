<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Railway terminates TLS at its proxy and forwards requests internally.
        // Trust its forwarded headers so Laravel recognizes the original HTTPS URL.
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \App\Http\Middleware\PreventBackHistory::class,
            \App\Http\Middleware\SetUserLocale::class,
        ]);
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'secretary.dashboard' => \App\Http\Middleware\EnhanceSecretaryDashboard::class,
            'attendance.filters' => \App\Http\Middleware\AddAttendancePeriodFilters::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $exception, \Illuminate\Http\Request $request) {
            if ($request->is('logout')) {
                \Illuminate\Support\Facades\Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('status', 'Umetoka kwenye akaunti salama.');
            }
        });
    })->create();
