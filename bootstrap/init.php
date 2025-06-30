<?php

declare(strict_types=1);

use Core\Database\DB;
use Core\Logger\Logger;
use Core\Support\App\App;
use Core\Support\Env\Env;
use Core\Support\Exception\ExceptionHandler;
use Core\Support\Session\Session;
use Core\Translator\Translator;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

date_default_timezone_set('Europe/Moscow');

require_once __DIR__ . '/../vendor/autoload.php';

try {
    Env::load(__DIR__ . '/../.env');
} catch (Exception $e) {
    Logger::error($e->getMessage());
    throw new Exception($e->getMessage());
}

$appPath = Env::get('APP_PATH');
if (!is_string($appPath)) {
    throw new \RuntimeException('APP_PATH environment variable is not set or not a string');
}

App::init($appPath);

Logger::setLogFile(App::getBasePath() . DIRECTORY_SEPARATOR . 'logs/logs.log');

Session::start();

$appLocale = Env::get('APP_LOCALE');
if (!is_string($appLocale)) {
    throw new \RuntimeException('APP_LOCALE environment variable is not set or not a string');
}

$language = language($appLocale);
Translator::init(App::getBasePath() . DIRECTORY_SEPARATOR . 'lang', $language);

$encryptionKey = Env::get('ENCRYPTION_KEY');
if (!is_string($encryptionKey)) {
    throw new \RuntimeException('ENCRYPTION_KEY environment variable is not set or not a string');
}

ExceptionHandler::register();

$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [
        App::getBasePath() . DIRECTORY_SEPARATOR . '/../app/domain/Auth/Domain/Model/Entities',
        App::getBasePath() . DIRECTORY_SEPARATOR . '/../app/domain/Notification/Domain/Model/Entities',
        App::getBasePath() . DIRECTORY_SEPARATOR . '/../app/domain/Task/Domain/Model/Entities',
    ],
    isDevMode: true,
);

$dbHost = Env::get('DB_HOST');
if (!is_string($dbHost)) {
    throw new \RuntimeException('DB_HOST environment variable is not set or not a string');
}

$dbUser = Env::get('DB_USERNAME');
if (!is_string($dbUser)) {
    throw new \RuntimeException('DB_USERNAME environment variable is not set or not a string');
}

$dbPassword = Env::get('DB_PASSWORD');
if (!is_string($dbPassword)) {
    throw new \RuntimeException('DB_PASSWORD environment variable is not set or not a string');
}

$dbName = Env::get('DB_DATABASE');
if (!is_string($dbName)) {
    throw new \RuntimeException('DB_DATABASE environment variable is not set or not a string');
}

$dbCharset = Env::get('DB_CHARSET');
if (!is_string($dbCharset)) {
    throw new \RuntimeException('DB_CHARSET environment variable is not set or not a string');
}

$dbPort = Env::get('DB_PORT');
if (!is_string($dbPort) && !is_int($dbPort)) {
    throw new \RuntimeException('DB_PORT environment variable is not set or not a string/int');
}

$dbPort = is_string($dbPort) ? (int)$dbPort : $dbPort;

$connection = DriverManager::getConnection([
    'driver' => 'pdo_mysql',
    'host' => $dbHost,
    'user' => $dbUser,
    'password' => $dbPassword,
    'dbname' => $dbName,
    'charset' => $dbCharset,
    'port' => $dbPort,
], $config);

$entityManager = new EntityManager($connection, $config);
DB::setEntityManager($entityManager);