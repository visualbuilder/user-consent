<?php

use Visualbuilder\FilamentUserConsent\Tests\Seeders\ConsentOptionSeeder;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;


it('can access user consent list page', function () {
    $this->seed(ConsentOptionSeeder::class);
    get(route('consent-option-request'))->assertSuccessful();
});
