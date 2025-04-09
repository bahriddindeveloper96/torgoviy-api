<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocaleFromHeader
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->header('Accept-Language');
        
        if ($locale) {
            // Get first part if locale contains region (e.g., 'en-US' -> 'en')
            $locale = explode('-', $locale)[0];
            
            // Check if locale exists in our allowed locales
            if (in_array($locale, config('app.locales', ['en', 'ru', 'uz']))) {
                app()->setLocale($locale);
            }
        }

        return $next($request);
    }
}
