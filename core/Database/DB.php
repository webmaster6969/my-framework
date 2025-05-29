<?php

declare(strict_types=1);

namespace Core\Database;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;

class DB
{
    private static ?EntityManager $em = null;

    public static function setEntityManager(EntityManager $entityManager): void
    {
        self::$em = $entityManager;
    }

    public static function getEntityManager(): ?EntityManager
    {
        return self::$em;
    }

    /**
     * @throws \Exception
     */
    public static function em(): EntityManager
    {
        if (!self::$em) {
            throw new \Exception("EntityManager not initialized in DB");
        }

        return self::$em;
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public static function raw(string $sql, array $params = []): array
    {
        $conn = self::em()->getConnection();
        return $conn->fetchAllAssociative($sql, $params);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public static function execute(string $sql, array $params = []): int
    {
        $conn = self::em()->getConnection();
        return $conn->executeStatement($sql, $params);
    }
}