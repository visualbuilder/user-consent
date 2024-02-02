<?php

namespace Ekoukltd\LaraConsent\Listeners;

use Ekoukltd\LaraConsent\Events\ConsentUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogConsentUpdated
{
    public function handle(ConsentUpdated $event)
    {
        if (($event->consentOption->is_mandatory && config('laraconsent.logging.mandatory'))
            || (! $event->consentOption->is_mandatory && config('laraconsent.logging.optional'))
        ) {
            $status = $event->accepted ? 'accepted' : 'refused';
            Log::info('Consent: ' . $event->consentOption->key . " $status by " . Auth::user()->email);
        }
    }
}
