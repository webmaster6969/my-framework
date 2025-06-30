#!/usr/bin/env php
<?php

use App\domain\Task\Application\ConsoleCommand\CheckEndTask;
use Core\Console\ConsoleKernel;

$router = require __DIR__ . '/bootstrap/init.php';

$kernel = new ConsoleKernel();
$kernel->register(new CheckEndTask());

$kernel->handle($argv);
