<?php

return [
    'default' => 'local',

    'disks' => [
        'local' => [
            'root' => __DIR__ . '/../storage/app',
        ],
        'public' => [
            'root' => __DIR__ . '/../storage/public',
        ],
    ],
];