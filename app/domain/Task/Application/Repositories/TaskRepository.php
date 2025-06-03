<?php

declare(strict_types=1);

namespace App\domain\Task\Application\Repositories;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Task\Domain\Model\Entities\Task;
use App\domain\Task\Domain\Repositories\TaskRepositoryInterface;
use Core\Database\DB;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class TaskRepository implements TaskRepositoryInterface
{
    /**
     * @var EntityManagerInterface|\Doctrine\ORM\EntityManager|null
     */
    private ?EntityManagerInterface $em;

    /**
     *
     */
    public function __construct()
    {
        $this->em = DB::getEntityManager();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function save(Task $task): bool
    {
        if ($this->em === null) {
            return false;
        }

        $this->em->persist($task);
        $this->em->flush();

        return true;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function delete(Task $task): bool
    {
        if ($this->em === null) {
            return false;
        }

        $this->em->remove($task);
        $this->em->flush();

        return true;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update(Task $task): bool
    {
        if ($this->em === null) {
            return false;
        }

        $this->em->persist($task);
        $this->em->flush();

        return true;
    }

    /**
     * @param User $user
     * @param int $task_id
     * @return Task|null
     * @phpstan-return Task|null
     */
    public function findByUser(User $user, int $task_id): ?Task
    {
        if ($this->em === null) {
            return null;
        }

        /** @var Task|null $task */
        $task = $this->em
            ->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->where('t.user = :userId')
            ->andWhere('t.id = :taskId')
            ->setParameter('taskId', $task_id)
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getOneOrNullResult();

        return $task;
    }

    /**
     * @return Task[]
     */
    public function findAll(): array
    {
        if ($this->em === null) {
            return [];
        }

        return $this->em->getRepository(Task::class)->findAll();
    }

    /**
     * @param User $user
     * @param int $page
     * @param int $limit
     * @return Task[]
     * @phpstan-return array<Task>
     */
    public function findByUserAll(User $user, int $page = 1, int $limit = 10): array
    {
        if ($this->em === null) {
            return [];
        }

        $page = max(1, $page);
        $offset = ($page - 1) * $limit;

        /** @var Task[] $tasks */
        $tasks = $this->em
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

        return $tasks;
    }

    /**
     * @param int $id
     * @return Task|null
     */
    public function find(int $id): ?Task
    {
        if ($this->em === null) {
            return null;
        }

        return $this->em->getRepository(Task::class)->find($id);
    }
}