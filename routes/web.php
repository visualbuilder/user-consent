<?php

use Filament\Http\Middleware\Authenticate;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Visualbuilder\FilamentUserConsent\Livewire\ConsentOptionRequest;

//Routes for users to view and save their consent

Route::middleware([
    EncryptCookies::class,
    StartSession::class,
    VerifyCsrfToken::class,
    AuthenticateSession::class,
    Authenticate::class,
    'auth:admin,practitioner,enduser'
])->group(function () {
    Route::get('consent-option-request', ConsentOptionRequest::class)->name('consent-option-request');
});
