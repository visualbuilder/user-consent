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

it('can skip optional consents', function() {
    $collections = auth()->user()->outstandingConsents();
    $fillForm = [];

    // Only fill mandatory consents, skip optional ones
    foreach($collections as $consentOption) {
        if($consentOption->is_mandatory) {
            $fillForm["consents.$consentOption->id"] = true;

            // Fill all required questions for mandatory consents
            if($consentOption->questions->count() > 0) {
                foreach ($consentOption->questions as $question) {
                    if($question->required) {
                        $fieldValue = match ($question->component) {
                            'text' => fake()->name(),
                            'email' => fake()->email(),
                            'number' => rand(1, 100),
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
    }

    livewire(ConsentOptionFormBuilder::class)
        ->fillForm($fillForm)
        ->call('submit')
        ->assertHasNoFormErrors();
});

it('validates missing mandatory consents', function() {
    $collections = auth()->user()->outstandingConsents();
    $fillForm = [];

    // Only fill optional consents, skip mandatory ones
    foreach($collections as $consentOption) {
        if(!$consentOption->is_mandatory) {
            $fillForm["consents.$consentOption->id"] = true;
        }
    }

    // Should fail validation because mandatory consents are not filled
    expect(function() use ($fillForm) {
        livewire(ConsentOptionFormBuilder::class)
            ->fillForm($fillForm)
            ->call('submit');
    })->toThrow(\Illuminate\Validation\ValidationException::class);
});

it('can skip optional questions', function() {
    $collections = auth()->user()->outstandingConsents();
    $fillForm = [];

    foreach($collections as $consentOption) {
        if($consentOption->is_mandatory) {
            $fillForm["consents.$consentOption->id"] = true;
        }

        // Only fill required questions, skip optional ones
        if($consentOption->questions->count() > 0) {
            foreach ($consentOption->questions as $question) {
                if($question->required) {
                    $fieldValue = match ($question->component) {
                        'text' => fake()->name(),
                        'email' => fake()->email(),
                        'number' => rand(1, 100),
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
});

it('validates missing required questions', function() {
    $collections = auth()->user()->outstandingConsents();
    $fillForm = [];

    // Find a consent with required questions
    $consentWithRequiredQuestions = $collections->first(function($consent) {
        return $consent->questions->contains('required', true);
    });

    expect($consentWithRequiredQuestions)->not->toBeNull();

    // Fill mandatory consents but skip required questions
    foreach($collections as $consentOption) {
        if($consentOption->is_mandatory) {
            $fillForm["consents.$consentOption->id"] = true;
        }

        // Only fill optional questions, skip required ones
        if($consentOption->questions->count() > 0) {
            foreach ($consentOption->questions as $question) {
                if(!$question->required) {
                    $fieldValue = match ($question->component) {
                        'text' => fake()->name(),
                        'email' => fake()->email(),
                        'number' => rand(1, 100),
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

    // Should fail validation because required questions are not filled
    expect(function() use ($fillForm) {
        livewire(ConsentOptionFormBuilder::class)
            ->fillForm($fillForm)
            ->call('submit');
    })->toThrow(\Illuminate\Validation\ValidationException::class);
});

it('validates individual required question fields', function() {
    $collections = auth()->user()->outstandingConsents();

    // Find a consent with required questions
    $consentWithRequiredQuestions = $collections->first(function($consent) {
        return $consent->questions->contains('required', true);
    });

    expect($consentWithRequiredQuestions)->not->toBeNull();

    $requiredQuestion = $consentWithRequiredQuestions->questions->where('required', true)->first();
    expect($requiredQuestion)->not->toBeNull();

    // Fill everything except one required question
    $fillForm = [];
    foreach($collections as $consentOption) {
        if($consentOption->is_mandatory) {
            $fillForm["consents.$consentOption->id"] = true;
        }

        if($consentOption->questions->count() > 0) {
            foreach ($consentOption->questions as $question) {
                // Skip the specific required question we're testing
                if($question->id === $requiredQuestion->id) {
                    continue;
                }

                if($question->required) {
                    $fieldValue = match ($question->component) {
                        'text' => fake()->name(),
                        'email' => fake()->email(),
                        'number' => rand(1, 100),
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

    // Should fail validation for the specific missing required question
    expect(function() use ($fillForm) {
        livewire(ConsentOptionFormBuilder::class)
            ->fillForm($fillForm)
            ->call('submit');
    })->toThrow(\Illuminate\Validation\ValidationException::class);
});
