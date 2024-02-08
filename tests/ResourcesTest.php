<?php

use Visualbuilder\FilamentUserConsent\Models\ConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\CreateConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\ListConsentOptions;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('can access user consent list page', function () {
    get(ConsentOptionResource::getUrl('index'))
        ->assertSuccessful();
});

it('can list user consents', function () {
    $consentOption = ConsentOption::factory()->count(10)->create();

    livewire(ListConsentOptions::class)
        ->assertCanSeeTableRecords($consentOption);
});

it('can access user consent create page', function () {
    get(ConsentOptionResource::getUrl('create'))
        ->assertSuccessful();
});

it('can create user consent', function () {
    $newData = ConsentOption::factory()->make();
    livewire(CreateConsentOption::class)
        ->fillForm([
            'title' => $newData->title,
            'label' => $newData->label,
            'sort_order' => 1,
            'enabled' => $newData->enabled,
            'is_mandatory' => $newData->is_mandatory,
            'force_user_update' => $newData->force_user_update,
            'published_at' => $newData->published_at,
            'models' => $newData->models,
            'text' => $newData->text,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(ConsentOption::class, [
        'title' => $newData->title,
        'label' => $newData->label,
        'sort_order' => 1,
        'enabled' => $newData->enabled,
        'is_mandatory' => $newData->is_mandatory,
        'force_user_update' => $newData->force_user_update,
        'published_at' => $newData->published_at,
        'models' => $newData->models,
        'text' => $newData->text,
    ]);
});
