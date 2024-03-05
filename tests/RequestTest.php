<?php

use Carbon\Carbon;
use Visualbuilder\FilamentUserConsent\Livewire\ConsentOptionFormBuilder;
use Visualbuilder\FilamentUserConsent\Models\ConsentOption;
use Visualbuilder\FilamentUserConsent\Tests\Seeders\ConsentOptionSeeder;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use Illuminate\Support\Arr;


it('can access user consent list page', function () {
    $this->seed(ConsentOptionSeeder::class);
    get(route('consent-option-request'))->assertSuccessful();
});


it('can generate dynamic form fields', function() {
    $this->seed(ConsentOptionSeeder::class);
    $collections = auth()->user()->outstandingConsents();

    $livewireComponent = livewire(ConsentOptionFormBuilder::class);
    foreach($collections as $consentOption){
        $livewireComponent->assertFormFieldExists("consents.$consentOption->id");

        if((int)$consentOption->additional_info === 1) {
            foreach ($consentOption->fields as $field) {
                $livewireComponent->assertFormFieldExists("consents_info.$consentOption->id.{$field['name']}");
            }
        }
    }
    $livewireComponent->call('submit');
    
});

it('validate mandatory user consents', function() {
    $this->seed(ConsentOptionSeeder::class);
    $collections = auth()->user()->outstandingConsents();
    $fieldValidation = [];
    foreach($collections as $consentOption){
        if($consentOption->is_mandatory) {
            $fieldValidation["consents.$consentOption->id"] = 'required';
        }
        if((int)$consentOption->additional_info === 1) {
            foreach ($consentOption->fields as $field) {
                if((bool)$field['required']) {
                    $fieldValidation["consents_info.$consentOption->id.{$field['name']}"] = "required";    
                }
            }
        }
    }

    livewire(ConsentOptionFormBuilder::class)
        ->call('submit')
        ->fillForm([])
        ->assertHasFormErrors($fieldValidation);
});


it('can fill and save consents', function() {
    $this->seed(ConsentOptionSeeder::class);
    $collections = auth()->user()->outstandingConsents();
    $fillForm = [];
    foreach($collections as $consentOption) {
        if($consentOption->is_mandatory) {
            $fillForm["consents.$consentOption->id"] = true;
        }
        if((int)$consentOption->additional_info === 1) {
            foreach ($consentOption->fields as $field) {
                if((bool)$field['required']) {
                    $fieldValue = match ($field['type']) {
                        'text' => fake()->name(),
                        'email' => fake()->email(),
                        'number' => rand(100, 10000000),
                        'select' => Arr::random(explode(',', $field['options'])),
                        'textarea' => fake()->sentence(),
                        'check' => fake()->boolean(),
                        'radio' => Arr::random(explode(',', $field['options'])),
                        'date' => Carbon::now()->subDays(rand(0, 10))->format('Y-m-d'),
                        'datetime' => Carbon::now()->subDays(rand(0, 10))->format('Y-m-d H:i:s'),
                    };
                    $fillForm["consents_info.$consentOption->id.{$field['name']}"] = $fieldValue;  
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