<?php

use Illuminate\Support\Facades\Route;
use Visualbuilder\FilamentUserConsent\Livewire\ConsentOptionRequest;

//Routes for users to view and save their consent
Route::middleware(['auth:admin,practitioner,enduser'])->group(function () {
    Route::get('consent-option-request', ConsentOptionRequest::class)->name('consent-option-request');
});
