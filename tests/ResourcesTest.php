<?php

use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource;

use function Pest\Laravel\get;

it('can access user consent list page', function () {
    get(ConsentOptionResource::getUrl('index'))
        ->assertSuccessful();
});
