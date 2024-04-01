<?php

use Filament\Http\Middleware\Authenticate;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Visualbuilder\FilamentUserConsent\Livewire\ConsentOptionFormBuilder;

//Routes for users to view and save their consent
Route::middleware([
    EncryptCookies::class,
    StartSession::class,
    ShareErrorsFromSession::class,
    VerifyCsrfToken::class,
    AuthenticateSession::class,
    'auth:'.config('filament-user-consent.auth-guards'),
])->group(function () {
    Route::get('consent-option-request', ConsentOptionFormBuilder::class)->name('consent-option-request');
});
