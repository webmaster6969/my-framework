<?php

use Core\Database\DB;
use Core\Support\App\App;
use Core\Support\Crypt\Crypt;
use Core\Support\Env\Env;
use Core\Support\Exception\ExceptionHandler;
use Core\Support\Session\Session;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once __DIR__ . '/../vendor/autoload.php';

Session::start();

Env::load();
App::init(Env::get('APP_PATH'));
new Crypt(Env::get('ENCRYPTION_KEY'));
ExceptionHandler::register();

// Create a simple "default" Doctrine ORM configuration for Attributes
$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [__DIR__ . '/../database/Entities'],
    isDevMode: true,
);
// or if you prefer XML
// $config = ORMSetup::createXMLMetadataConfiguration(
//    paths: [__DIR__ . '/config/xml'],
//    isDevMode: true,
//);

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