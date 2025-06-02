<?php

declare(strict_types=1);

use Core\Database\DB;
use Core\Logger\Logger;
use Core\Support\App\App;
use Core\Support\Crypt\Crypt;
use Core\Support\Env\Env;
use Core\Support\Exception\ExceptionHandler;
use Core\Support\Session\Session;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once __DIR__ . '/../vendor/autoload.php';

Logger::setLogFile(__DIR__ . '/../logs/logs.log');

Session::start();

Env::load();
App::init(Env::get('APP_PATH'));
new Crypt(Env::get('ENCRYPTION_KEY'));
ExceptionHandler::register();

// Create a simple "default" Doctrine ORM configuration for Attributes
$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [
        __DIR__ . '/../app/domain/Auth/Domain/Model/Entities',
        __DIR__ . '/../app/domain/Notification/Domain/Model/Entities',
        __DIR__ . '/../app/domain/Task/Domain/Model/Entities',
    ],
    isDevMode: true,
);

// configuring the database connection
$connection = DriverManager::getConnection([
    'driver' => 'pdo_mysql',
    'host' => Env::get('DB_HOST'),
    'user' => Env::get('DB_USERNAME'),
    'password' => Env::get('DB_PASSWORD'),
    'dbname' => Env::get('DB_DATABASE'),
    'charset' => Env::get('DB_CHARSET'),
    'port' => Env::get('DB_PORT'),
], $config);

// obtaining the entity manager
$entityManager = new EntityManager($connection, $config);
DB::setEntityManager($entityManager);
require __DIR__ . '/../app/Http/Routes/web.php';