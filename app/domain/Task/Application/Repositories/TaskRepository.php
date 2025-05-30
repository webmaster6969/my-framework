<?php

declare(strict_types=1);

namespace App\domain\Task\Application\Repositories;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Task\Domain\Model\Entities\Task;
use App\domain\Task\Domain\Repositories\TaskRepositoryInterface;
use Core\Database\DB;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class TaskRepository implements TaskRepositoryInterface
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function save(Task $task): bool
    {
        DB::getEntityManager()->persist($task);
        DB::getEntityManager()->flush();

        return true;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function delete(Task $task): bool
    {
        DB::getEntityManager()->remove($task);
        DB::getEntityManager()->flush();

        return true;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update(Task $task): bool
    {
        DB::getEntityManager()->persist($task);
        DB::getEntityManager()->flush();

        return true;
    }

    public function findByUser(User $user, int $task_id): ?Task
    {
        return DB::getEntityManager()
            ->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->where('t.user = :userId')
            ->andWhere('t.id = :taskId')
            ->setParameter('taskId', $task_id)
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAll()
    {
        // TODO: Implement findAll() method.
    }

    public function findByUserAll(User $user, int $page = 1, int $limit = 10): array
    {
        $page = max(1, $page); // Защита от нуля и отрицательных значений
        $offset = ($page - 1) * $limit;

        return DB::getEntityManager()
            ->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->where('t.user = :userId')
            ->setParameter('userId', $user->getId())
            ->orderBy('t.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }
}