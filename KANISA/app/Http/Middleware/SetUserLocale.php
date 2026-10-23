<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetUserLocale
{
    /**
     * Apply the signed-in user's saved language preference to each request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->user()?->language;

        if (in_array($locale, ['sw', 'en'], true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
