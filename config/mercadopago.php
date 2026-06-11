<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mercado Pago Credentials
    |--------------------------------------------------------------------------
    |
    | Credenciales de la cuenta de Mercado Pago.
    | Usar SIEMPRE las credenciales de PRUEBA (empiezan con TEST-) para sandbox.
    | Nunca hardcodear estos valores — sólo a través de variables de entorno.
    |
    */

    'access_token' => env('MP_ACCESS_TOKEN'),
    'public_key'   => env('MP_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Sandbox Mode
    |--------------------------------------------------------------------------
    |
    | true  → usa sandbox_init_point y credenciales TEST-
    | false → usa init_point y credenciales reales (producción)
    |
    */

    'sandbox' => env('MP_SANDBOX', true),

    /*
    |--------------------------------------------------------------------------
    | Mobile App URL Scheme
    |--------------------------------------------------------------------------
    |
    | Scheme del deep link de la app mobile, registrado en app.json como "scheme".
    | Se usa para las back_urls de Mercado Pago en mobile checkout.
    | En Expo Go, Expo registra este scheme en el dispositivo automáticamente.
    |
    */

    'mobile_scheme' => env('MOBILE_APP_SCHEME', 'bodyfix'),

];
