<?php

use Core\Database\DB;
use Core\Support\Env;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

require_once __DIR__ . '/vendor/autoload.php';

Env::load();

// Create a simple "default" Doctrine ORM configuration for Attributes
$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [__DIR__ . '/database/entities'],
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

ConsoleRunner::run(
    new SingleManagerProvider(DB::getEntityManager())
);