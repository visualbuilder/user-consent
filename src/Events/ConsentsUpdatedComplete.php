<?php

namespace Visualbuilder\FilamentUserConsent\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConsentsUpdatedComplete
{
    use Dispatchable;
    use SerializesModels;

    public $consentOptions;

    public $user;

    public function __construct($consentOptions, $user)
    {
        $this->consentOptions = $consentOptions;
        $this->user = $user;
    }
}
