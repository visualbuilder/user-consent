<?php

use Visualbuilder\FilamentUserConsent\Livewire\ConsentOptionFormBuilder;
use Visualbuilder\FilamentUserConsent\Models\ConsentOption;
use Visualbuilder\FilamentUserConsent\Tests\Seeders\ConsentOptionSeeder;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;


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
        ->assertHasFormErrors()
        ->assertHasFormErrors($fieldValidation);
});


it('can fill and save consents', function() {
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
        ->assertHasFormErrors()
        ->assertHasFormErrors($fieldValidation);
});