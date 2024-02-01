<?php

return [

    //To which authenticatable models consents should be applied?
    'models' => [
        App\Models\Admin::class,
        App\Models\Practitioner::class,
        App\Models\EndUser::class
    ],

    'options' => [
        'App\Models\Admin' => 'Admin',
        'App\Models\Practitioner' => 'Practitioner',
        'App\Models\EndUser' => 'EndUser',
    ],

    'routes' => [
        'prefix' => 'consent-options',
    ],

    'navigation' => [
        'sort' => 50,
        'group' => 'Content',
    ],
];
