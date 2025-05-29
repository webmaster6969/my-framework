<?php

declare(strict_types=1);

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