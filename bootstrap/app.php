<?php

use Core\Support\Env;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/Support/helpers.php';

Env::load();

$router = require __DIR__ . '/../app/Http/Routes/web.php';

return $router;
