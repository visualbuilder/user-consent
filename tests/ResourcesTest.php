<?php

use function Pest\Laravel\get;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource;

it('can access user consent list page', function () {
    get(ConsentOptionResource::getUrl('index'))
        ->assertSuccessful();
});
