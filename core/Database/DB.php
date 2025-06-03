<?php

declare(strict_types=1);

namespace Core\Database;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;

class DB
{
    /**
     * @var EntityManager|null
     */
    private static ?EntityManager $em = null;

    /**
     * @param EntityManager $entityManager
     * @return void
     */
    public static function setEntityManager(EntityManager $entityManager): void
    {
        self::$em = $entityManager;
    }

    /**
     * @return EntityManager|null
     */
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
     * @param string $sql
     * @param array<string, mixed> $params
     * @return list<array<string,mixed>>
     * @throws Exception
     * @throws \Exception
     */
    public static function raw(string $sql, array $params = []): array
    {
        $conn = self::em()->getConnection();
        return $conn->fetchAllAssociative($sql, $params);
    }

    /**
     * @param string $sql
     * @param array<string, mixed> $params
     * @return int|string
     * @throws Exception
     * @throws \Exception
     */
    public static function execute(string $sql, array $params = []): int|string
    {
        $conn = self::em()->getConnection();
        return $conn->executeStatement($sql, $params);
    }
}