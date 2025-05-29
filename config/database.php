<?php

declare(strict_types=1);

use Core\Support\Env\Env;

return [
    'driver'   => 'pdo_mysql',
    'host'     => Env::get('DB_HOST', 'localhost'),
    'user'     => Env::get('DB_USERNAME', 'test'),
    'password' => Env::get('DB_PASSWORD', 'test'),
    'dbname'   => Env::get('DB_DATABASE', 'test'),
];