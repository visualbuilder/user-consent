<?php

use Visualbuilder\FilamentUserConsent\Models\ConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\CreateConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\EditConsentOption;
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
    $formData = [
        'key' => $newData->key,
        'title' => $newData->title,
        'label' => $newData->label,
        'sort_order' => 1,
        'enabled' => $newData->enabled,
        'is_mandatory' => $newData->is_mandatory,
        'force_user_update' => $newData->force_user_update,
        'published_at' => $newData->published_at,
        'models' => $newData->models,
        'text' => $newData->text,
    ];

    livewire(CreateConsentOption::class)
        ->fillForm($formData)
        ->call('create')
        ->assertHasNoFormErrors();

    $formData['models'] = json_encode($newData->models);
    $this->assertDatabaseHas(ConsentOption::class, $formData);
});

it('can access user consent edit page', function () {
    get(ConsentOptionResource::getUrl('edit', [
        'record' => ConsentOption::factory()->create(),
    ]))->assertSuccessful();
});

it('can update user consent', function () {
    $data = ConsentOption::factory()->create();
    $newData = ConsentOption::factory()->make();

    $formData = [
        'key' => $newData->key,
        'title' => $newData->title,
        'label' => $newData->label,
        'sort_order' => 1,
        'enabled' => $newData->enabled,
        'is_mandatory' => $newData->is_mandatory,
        'force_user_update' => $newData->force_user_update,
        'published_at' => $newData->published_at,
        'models' => $newData->models,
        'text' => $newData->text,
    ];

    livewire(EditConsentOption::class, [
        'record' => $data->getRouteKey(),
    ])
        ->fillForm($formData)
        ->call('save')
        ->assertHasNoFormErrors();

    $formData['models'] = json_encode($newData->models);
    $this->assertDatabaseHas(ConsentOption::class, $formData);
});
