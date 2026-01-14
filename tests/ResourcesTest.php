<?php

use Visualbuilder\FilamentUserConsent\Models\ConsentOption;
use Visualbuilder\FilamentUserConsent\Models\ConsentOptionQuestion;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\CreateConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\EditConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\ListConsentOptions;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\RelationManagers\ConsentOptionQuestionsRelationManager;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
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

    assertDatabaseHas(ConsentOption::class, $formData);
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

    assertDatabaseHas(ConsentOption::class, $formData);
});

it('can load the questions relation manager in consents', function () {
    $consentOption = ConsentOption::factory()->create();

    livewire(ConsentOptionQuestionsRelationManager::class, [
        'ownerRecord' => $consentOption,
        'pageClass' => EditConsentOption::class,
    ])
        ->assertSuccessful();
});

it('can list questions in consents', function () {
    $consentOption = ConsentOption::factory()->create();
    $question = ConsentOptionQuestion::create([
        'consent_option_id' => $consentOption->id,
        'component' => 'text',
        'name' => 'old_name',
        'label' => 'Old Label',
        'required' => false,
        'sort' => 1,
    ]);
    livewire(ConsentOptionQuestionsRelationManager::class, [
        'ownerRecord' => $consentOption,
        'pageClass' => EditConsentOption::class,
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$question]);
});

it('can create question in consents', function () {
    $consentOption = ConsentOption::factory()->create();
    $consentQuestionData = [
        'component' => 'text',
        'name' => 'test_question',
        'label' => 'Test Question Label',
        'required' => true,
        'sort' => 1,
    ];

    assertDatabaseMissing(
        table: ConsentOptionQuestion::class,
        data: $consentQuestionData + [
            'consent_option_id' => $consentOption->id,
        ]
    );

    livewire(ConsentOptionQuestionsRelationManager::class, [
        'ownerRecord' => $consentOption,
        'pageClass' => EditConsentOption::class,
    ])
        ->assertSuccessful()
        ->callTableAction('create', data: $consentQuestionData)
        ->assertHasNoTableActionErrors();

    assertDatabaseHas(
        table: ConsentOptionQuestion::class,
        data: $consentQuestionData + [
            'consent_option_id' => $consentOption->id,
        ]
    );
});

it('can update question in consents', function () {
    $consentOption = ConsentOption::factory()->create();
    $consentQuestionData = [
        'component' => 'text',
        'name' => 'old_name',
        'label' => 'Old Label',
        'required' => false,
        'sort' => 1,
    ];
    $consentQuestion = ConsentOptionQuestion::create(
        $consentQuestionData + [
            'consent_option_id' => $consentOption->id
        ]
    );

    $updatedQuestionData = [
        'component' => 'email',
        'name' => 'new_name',
        'label' => 'New Label',
        'required' => true,
        'sort' => 2,
    ];

    assertDatabaseHas(
        table: ConsentOptionQuestion::class,
        data: $consentQuestionData + [
            'consent_option_id' => $consentOption->id,
        ]
    );

    assertDatabaseMissing(
        table: ConsentOptionQuestion::class,
        data: $updatedQuestionData + [
            'consent_option_id' => $consentOption->id,
        ]
    );

    livewire(ConsentOptionQuestionsRelationManager::class, [
        'ownerRecord' => $consentOption,
        'pageClass' => EditConsentOption::class,
    ])
        ->assertSuccessful()
        ->callTableAction('edit', $consentQuestion, data: $updatedQuestionData)
        ->assertHasNoTableActionErrors();

    assertDatabaseHas(
        table: ConsentOptionQuestion::class,
        data: $updatedQuestionData + [
            'consent_option_id' => $consentOption->id,
        ]
    );
});

it('can delete question in consents', function () {
    $consentOption = ConsentOption::factory()->create();
    $question = ConsentOptionQuestion::create([
        'consent_option_id' => $consentOption->id,
        'component' => 'text',
        'name' => 'to_delete',
        'label' => 'Question to Delete',
        'required' => false,
        'sort' => 1,
    ]);

    $questionId = $question->id;

    livewire(ConsentOptionQuestionsRelationManager::class, [
        'ownerRecord' => $consentOption,
        'pageClass' => EditConsentOption::class,
    ])
        ->assertSuccessful()
        ->callTableAction('delete', $question)
        ->assertHasNoTableActionErrors();

    expect(ConsentOptionQuestion::find($questionId))->toBeNull();
});
