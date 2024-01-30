<?php

return [

    //To which authenticatable models consents should be applied?
    'models' => [
        'App\Models\EndUser',
        'App\Models\Admin',
    ],

    'routes'     => [
        'prefix'     => 'consent-options',
    ],
];
