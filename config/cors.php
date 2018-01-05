<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */

    'supportsCredentials' => false,
    'allowedOrigins' => ['*'],
    'allowedOriginsPatterns' => [],
    'allowedHeaders' => ['Authorization', 'Content-Type'],
    'allowedMethods' => ['DELETE', 'GET', 'POST', 'PUT'],
    'exposedHeaders' => [],
    'maxAge' => 86400,

];
