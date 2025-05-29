<?php

declare(strict_types=1);

namespace App\domain\Task\Application\Repositories;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Task\Domain\Model\Entities\Task;
use App\domain\Task\Domain\Repositories\TaskRepositoryInterface;
use Core\Database\DB;

class TaskRepository implements TaskRepositoryInterface
{
    public function save(Task $task): bool
    {
        DB::getEntityManager()->persist($task);
        DB::getEntityManager()->flush();

        return true;
    }

    public function delete($task)
    {
        // TODO: Implement delete() method.
    }

    public function update($task)
    {
        // TODO: Implement update() method.
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function findAll()
    {
        // TODO: Implement findAll() method.
    }

    public function findByUser(User $user, int $page = 1, int $limit = 10): array
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
}