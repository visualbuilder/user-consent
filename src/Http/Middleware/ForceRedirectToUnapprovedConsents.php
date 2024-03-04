<?php

namespace Visualbuilder\FilamentUserConsent\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForceRedirectToUnapprovedConsents
{
    public function handle(Request $request, Closure $next)
    {
        // Get the currently authenticated user across all guards
        $user = Auth::user();

        // If no user is authenticated, proceed with the request
        if (!$user) {
            return $next($request);
        }

        

        // Determine if the current route is exempt (a consent route or ends with '.logout')
        $route = $request->route()->getName();
        $isExemptRoute = str_contains($route, 'consent-options') || str_ends_with($route, '.logout');

        // Check for required consents if user is authenticated and not on an exempt or logout route
        if (!$isExemptRoute && !$request->ajax() && method_exists($user, 'hasRequiredConsents') && !$user->hasRequiredConsents()) {
            // Save the current request URL
            $request->session()->put('url.saved', $request->fullUrl());

            // Redirect user to ask for consent
            return redirect()->route('consent-option-request');
        }

        return $next($request);
    }

}
