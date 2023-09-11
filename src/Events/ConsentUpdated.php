<?php

namespace Visualbuilder\FilamentUserConsent\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Visualbuilder\FilamentUserConsent\Models\ConsentOption;

class ConsentUpdated
{
    use Dispatchable, SerializesModels;

    public $consentOption;
    public $accepted;


    public function __construct(ConsentOption $consentOption, $accepted)
    {
        $this->consentOption = $consentOption;
        $this->accepted = $accepted;
    }
}
