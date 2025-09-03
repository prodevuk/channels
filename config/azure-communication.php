<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Azure Communication Service Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Azure Communication Service settings.
    | You can get these values from your Azure portal.
    |
    */

    'connection_string' => env('AZURE_COMMUNICATION_CONNECTION_STRING'),

    /*
    |--------------------------------------------------------------------------
    | Email Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your email sender address. This must be a verified domain
    | in your Azure Communication Service resource.
    |
    */

    'email_sender' => env('AZURE_COMMUNICATION_EMAIL_SENDER'),

    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your SMS sender phone number. This must be a verified phone
    | number in your Azure Communication Service resource.
    |
    */

    'sms_sender' => env('AZURE_COMMUNICATION_SMS_SENDER'),

    /*
    |--------------------------------------------------------------------------
    | Default Options
    |--------------------------------------------------------------------------
    |
    | Default options for sending notifications.
    |
    */

    'defaults' => [
        'enable_delivery_report' => true,
        'timeout' => 30,
    ],
];
