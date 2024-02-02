<?php

namespace Visualbuilder\FilamentUserConsent\Listeners;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Visualbuilder\FilamentUserConsent\Events\ConsentsUpdatedComplete;
use Visualbuilder\FilamentUserConsent\Notifications\ConsentsUpdatedNotification;

class NotifyConsentsUpdated
{
    public function handle(ConsentsUpdatedComplete $event)
    {
        $event->user->notify(new ConsentsUpdatedNotification());
    }
}