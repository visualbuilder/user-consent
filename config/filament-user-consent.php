<?php

use Visualbuilder\FilamentUserConsent\Listeners\NotifyConsentsUpdated;

return [

    //To which authenticatable models consents should be applied?
    'models' => [
        App\Models\User::class,
    ],

    'auth-guards' => 'web',

    'options' => [
        App\Models\User::class => 'User',
    ],

    'routes' => [
        'prefix' => 'consent-options',
    ],

    'navigation' => [
        'consent_options'   => [
            'page_heading' => 'Manage Consent Options',
            'sort'         => 30,
            'label'        => 'Consent Options',
            'icon'         => 'heroicon-o-check-badge',
            'group'        => 'Content',
            'cluster'      => false,
            'position'     => false,
            'register'     => true,
        ],
        'consent_responses' => [
            'page_heading' => 'User Consents',
            'sort'         => 400,
            'label'        => 'Consent Responses',
            'icon'         => 'heroicon-o-check-badge',
            'group'        => 'Content',
            'cluster'      => false,
            'position'     => false,
            'register'     => true,
        ],
    ],


    //send user an email with a copy of the consent after saving.
    'notify'     => ['mail'],

    'email-template' => 'vendor.user-consent.layouts.email',

    //The mailable class to use for sending consent notification
    'mailable'       => \Visualbuilder\FilamentUserConsent\Mail\ConsentsUpdatedMail::class,

    'components' => [
        'placeholder' => 'Placeholder',
        'likert'      => 'Likert Slider',
        'text'        => 'Free Text Input',
        'email'       => 'Email Input',
        'number'      => 'Number Input',
        'textarea'    => 'Text area',
        'select'      => 'Select dropdown',
        'radio'       => 'Radio options',
        'check'       => 'Checkbox',
        'date'        => 'Date Picker',
        'datetime'    => 'Date & Time Picker',
    ],

    'autofill_columns' => [
        'salutation'   => 'Title',
        'full_name'    => 'Full Name',
        'email'        => 'Email address',
        'mobile'       => 'Mobile number',
        'phone_number' => 'Phone number',
        'full_address' => "Full address"
    ],

];
