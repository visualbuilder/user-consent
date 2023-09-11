<?php

namespace Visualbuilder\FilamentUserConsent\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class ConsentsUpdatedComplete
{
    use Dispatchable, SerializesModels;

    public $consentOptions;
    public $user;

    public function __construct($consentOptions,$user)
    {
        $this->consentOptions = $consentOptions;
        $this->user = $user;
    }
}
