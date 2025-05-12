<?php

use Core\Support\Env;
use Core\Support\ExceptionHandler;
use Core\Support\Session;

require_once __DIR__ . '/../vendor/autoload.php';

Session::start();

Env::load();
ExceptionHandler::register();

require_once __DIR__ . '/../core/Support/helpers.php';

require __DIR__ . '/../app/Http/Routes/web.php';