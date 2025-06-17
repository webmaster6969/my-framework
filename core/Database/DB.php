<?php

declare(strict_types=1);

namespace Core\Database;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;

class DB
{
    /**
     * @var EntityManager
     */
    private static EntityManager $em;

    /**
     * @param EntityManager $entityManager
     * @return void
     */
    public static function setEntityManager(EntityManager $entityManager): void
    {
        self::$em = $entityManager;
    }

    /**
     * @return EntityManager
     */
    public static function getEntityManager(): EntityManager
    {
        return self::$em;
    }

    /**
     * @return EntityManager
     */
    public static function em(): EntityManager
    {
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

    /**
     * @return void
     * @throws Exception
     */
    public static function beginTransaction(): void
    {
        self::em()->getConnection()->beginTransaction();
    }

    /**
     * @return void
     * @throws Exception
     */
    public static function commit(): void
    {
        self::em()->getConnection()->commit();
    }

    /**
     * Откатить транзакцию
     *
     * @return void
     * @throws Exception
     */
    public static function rollback(): void
    {
        self::em()->getConnection()->rollBack();
    }

    /**
     * @return bool
     */
    public static function inTransaction(): bool
    {
        return self::em()->getConnection()->isTransactionActive();
    }
}