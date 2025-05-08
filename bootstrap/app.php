<?php

use Core\Support\Env;
use Core\Support\ExceptionHandler;

require_once __DIR__ . '/../vendor/autoload.php';

Env::load();
ExceptionHandler::register();

require_once __DIR__ . '/../core/Support/helpers.php';

require __DIR__ . '/../app/Http/Routes/web.php';