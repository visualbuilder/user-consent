<?php

namespace Visualbuilder\FilamentUserConsent\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForceRedirectToUnapprovedConsents
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('admin')->check()) {
            $guard = 'admin';
        } elseif (Auth::guard('enduser')->check()) {
            $guard = 'enduser';
        } elseif (Auth::guard('practitioner')->check()) {
            $guard = 'practitioner';
        }

        $isConsentRoute = str_contains($request->route()->getName(), 'consent-options');
        if (
            //must be logged in
            Auth::guard($guard)->user()
            //have the trait installed
            && method_exists(Auth::guard($guard)->user(), 'hasRequiredConsents')
            //Not be a consent route
            && ! $isConsentRoute
            //Not an ajax call
            && ! $request->ajax()
            //Not have required consents signed
            && ! Auth::guard($guard)->user()->hasRequiredConsents()
        ) {
            //Save current request URL
            $request->session()->put('url.saved', $request->fullUrl());

            //Redirect user to ask for consent
            return redirect()->route('consent-option-request');
        }

        return $next($request);
    }
}
