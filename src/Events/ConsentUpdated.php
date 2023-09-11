<?php

namespace Visualbuilder\FilamentUserConsent\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Visualbuilder\FilamentUserConsent\Models\ConsentOption;

class ConsentUpdated
{
    use Dispatchable;
    use SerializesModels;

    public $consentOption;

    public $accepted;

    public function __construct(ConsentOption $consentOption, $accepted)
    {
        $this->consentOption = $consentOption;
        $this->accepted = $accepted;
    }
}
