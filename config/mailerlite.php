<?php

return [
    'endpoint' => env('MAILERLITE_API_ENDPOINT'),
    'token' => env('MAILERLITE_API_TOKEN'),
    'endpoints' => [
        'subscribers' => [
            'get' =>  '/subscribers'
        ]
    ],
    'fields' => [
        'name',
        'email',
        'country',
        'date_subscribe',
    ],
    
];
