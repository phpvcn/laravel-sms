<?php

/*
 * https://github.com/phpvcn/Laravel-sms/wiki/#docs-configuration for more information.
 */
return [
    'driver' => env('SMS_DRIVER', 'log'),

    'meilian' => [
        'api_user'  => env('MEILIAN_API_USER', 'Your MEILIAN API Username'),
        'api_pass'  => env('MEILIAN_API_PASS', 'Your MEILIAN API Password'),
        'api_key'   => env('MEILIAN_API_KEY', 'Your MEILIAN API Key'),
    ],

    'luosimao' => [
        'api_key' => env('LUOSIMAO_API_KEY', 'Your Luosimao Api Key'),
        'sign' => env('LUOSIMAO_API_SIGN', 'Your Luosimao Sign'),
    ],
];
