<?php

return [
    'MESSAGE_BROKER_LIST' => 
    [
        'REDIS'=>'REDIS',
        'KAFKA'=>'KAFKA'
    ],
    'CURRENT_MESSAGE_BROKER' => env('MESSAGE_BROKER', 'kafka'),
];