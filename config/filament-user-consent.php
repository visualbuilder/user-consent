<?php

use Visualbuilder\FilamentUserConsent\Events\ConsentsUpdatedComplete;
use Visualbuilder\FilamentUserConsent\Events\ConsentUpdated;
use Visualbuilder\FilamentUserConsent\Listeners\NotifyConsentsUpdated;

return [

    //To which authenticatable models consents should be applied?
    'models' => [
        App\Models\Admin::class,
        App\Models\Practitioner::class,
        App\Models\EndUser::class,
    ],

    'options' => [
        'App\Models\Admin' => 'Admin',
        'App\Models\Practitioner' => 'Practitioner',
        'App\Models\EndUser' => 'EndUser',
    ],

    'notify' => ['mail'],

    'routes' => [
        'prefix' => 'consent-options',
    ],

    'navigation' => [
        'sort' => 50,
        'group' => 'Content',
    ],

    'listeners' => [
        //Event triggered after a consent updated
        ConsentUpdated::class => [
            // Default listeners for this event
            // You may want to update mailchump if consent withdrawn for marketing
        ],
        //Event triggered after all consents updated
        ConsentsUpdatedComplete::class => [
            NotifyConsentsUpdated::class,
        ],
    ],

    //send user an email with a copy of the consent after saving.
    'notify' => ['mail'],

    'email-template' => 'vendor.ekoukltd.laraconsent.layouts.email',
];
