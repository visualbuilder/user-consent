<?php

use Visualbuilder\FilamentUserConsent\Models\ConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\CreateConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\EditConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\ListConsentOptions;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('can access user consent list page', function () {
    get(route('consent-option-request'))->assertSuccessful();
});
