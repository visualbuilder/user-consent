<?php

namespace Visualbuilder\FilamentUserConsent\Infolists\Components;

use Filament\Infolists\Components\Entry;

class ConsentAcceptForm extends Entry
{
    protected string $view = 'user-consent::infolists.components.consent-accept-form';

    public $errorBag = [];

    protected $rules = [
        'name' => 'required|min:6',
        'email' => 'required|email',
    ];
 
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
 
    public function submit()
    {
        $validatedData = $this->validate();
 
    }
}
