<?php

return [
    'private_key' => storage_path('jwtRS256.key'),

    'public_key' => storage_path('jwtRS256.key.pub'),

    'ttl' => 86400, // in seconds

    'leeway' => 60, // in seconds

    'encrypt_algo' => 'RS256',

    'allowed_algo' => ['RS256']
];
