<?php

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Visualbuilder\FilamentUserConsent\Livewire\ConsentOptionFormBuilder;

//Routes for users to view and save their consent
//The page component is resolvable from config so host apps can register their
//own subclass (e.g. to customise the page chrome) without overriding the route.
$consentOptionFormBuilder = config('filament-user-consent.pages.consent_option_form_builder', ConsentOptionFormBuilder::class);

Route::middleware(['web', 'auth:' . config('filament-user-consent.auth-guards')])
    ->group(function () use ($consentOptionFormBuilder) {
        Route::get('consent-option-request', $consentOptionFormBuilder)->name('consent-option-request');
    });
