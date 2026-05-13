<?php

return [

    'paths' => [
        'api/*',
        'broadcasting/auth',
        'sanctum/csrf-cookie'
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://user-management-by-laravel-vue-production.up.railway.app',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];