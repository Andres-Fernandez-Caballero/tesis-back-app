<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WEB Keys
    |--------------------------------------------------------------------------
    |
    | WEB keys are used for authenticating push notifications sent to web
    | browsers.
    |
    */
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
        'admin_email' => env('VAPID_ADMIN_EMAIL'),
];