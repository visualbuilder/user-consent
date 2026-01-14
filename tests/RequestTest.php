<?php

use Carbon\Carbon;
use Visualbuilder\FilamentUserConsent\Livewire\ConsentOptionFormBuilder;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('can access user consent list page', function () {
    get(route('consent-option-request'))->assertSuccessful();
});

it('can generate dynamic form fields', function() {
    $collections = auth()->user()->outstandingConsents();

    expect($collections)->not->toBeEmpty();

    $livewireComponent = livewire(ConsentOptionFormBuilder::class);

    foreach($collections as $consentOption){
        // Assert that the consent toggle field exists
        $livewireComponent->assertFormFieldExists("consents.$consentOption->id");

        // Check for question-based fields (new system)
        if($consentOption->questions->count() > 0) {
            foreach ($consentOption->questions as $question) {
                $livewireComponent->assertFormFieldExists("consents_info.$consentOption->id.$question->id.$question->name");
            }
        }
    }
});

it('validate mandatory user consents', function() {
    $collections = auth()->user()->outstandingConsents();

    // Ensure there are mandatory consents to validate
    $hasMandatory = $collections->contains(fn($c) => $c->is_mandatory);
    expect($hasMandatory)->toBeTrue();

    // Submitting without filling mandatory fields should throw ValidationException
    expect(function() {
        livewire(ConsentOptionFormBuilder::class)
            ->call('submit');
    })->toThrow(\Illuminate\Validation\ValidationException::class);
});

it('can fill and save consents', function() {
    $collections = auth()->user()->outstandingConsents();
    $fillForm = [];

    foreach($collections as $consentOption) {
        if($consentOption->is_mandatory) {
            $fillForm["consents.$consentOption->id"] = true;
        }

        // Handle question-based fields (new system)
        if($consentOption->questions->count() > 0) {
            foreach ($consentOption->questions as $question) {
                if($question->required) {
                    $fieldValue = match ($question->component) {
                        'text' => fake()->name(),
                        'email' => fake()->email(),
                        'number' => rand(100, 10000000),
                        'select', 'radio', 'likert' => $question->options->first()?->id ?? 1,
                        'textarea' => fake()->sentence(),
                        'check' => true,
                        'date' => Carbon::now()->subDays(rand(0, 10))->format('Y-m-d'),
                        'datetime' => Carbon::now()->subDays(rand(0, 10))->format('Y-m-d H:i:s'),
                        default => fake()->word(),
                    };
                    $fillForm["consents_info.$consentOption->id.$question->id.$question->name"] = $fieldValue;
                }
            }
        }
    }

    livewire(ConsentOptionFormBuilder::class)
        ->fillForm($fillForm)
        ->call('submit')
        ->assertHasNoFormErrors();

    get(route('consent-option-request'))->assertForbidden();
});
