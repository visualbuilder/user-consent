<?php

namespace Visualbuilder\FilamentUserConsent\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForceRedirectToUnapprovedConsents
{
    public function handle(Request $request, Closure $next)
    {
        $isConsentRoute = str_contains($request->route()->getName(), 'consent-options');

        if (
            //must be logged in
            Auth::user()
            //have the trait installed
            && method_exists(Auth::user(), 'hasRequiredConsents')
            //Not be a consent route
            && ! $isConsentRoute
            //Not an ajax call
            && ! $request->ajax()
            //Not have required consents signed
            && ! Auth::user()->hasRequiredConsents()
        ) {
            //Save current request URL
            // $request->session()->put('url.saved', $request->fullUrl());
            //Redirect user to ask for consent
            return redirect()->route('consent-option-request');
        }

        return $next($request);
    }
}
