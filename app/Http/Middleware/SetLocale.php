<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.supported_locales');

        $locale = $request->session()->get('locale');

        if (! is_string($locale) || ! in_array($locale, $supportedLocales, true)) {
            $locale = $request->getPreferredLanguage($supportedLocales) ?? config('app.fallback_locale');
            $request->session()->put('locale', $locale);
        }

        App::setLocale($locale);

        return $next($request);
    }
}
