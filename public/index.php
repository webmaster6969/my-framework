<?php

$router = require __DIR__ . '/../bootstrap/app.php';

$router->dispatch($_SERVER['REQUEST_URI']);
