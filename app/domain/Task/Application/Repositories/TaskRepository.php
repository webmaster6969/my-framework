<?php

declare(strict_types=1);

namespace App\domain\Task\Application\Repositories;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Task\Domain\Model\Entities\Task;
use App\domain\Task\Domain\Repositories\TaskRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class TaskRepository implements TaskRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Task $task
     * @return bool
     */
    public function save(Task $task): bool
    {
        $this->em->persist($task);
        $this->em->flush();

        return true;
    }

    /**
     * @param Task $task
     * @return bool
     */
    public function delete(Task $task): bool
    {
        $this->em->remove($task);
        $this->em->flush();

        return true;
    }

    /**
     * @param Task $task
     * @return bool
     */
    public function update(Task $task): bool
    {
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
        return $this->em->getRepository(Task::class)->findAll();
    }

    /**
     * @param User $user
     * @param int $page
     * @param int $limit
     * @return Task[]
     * @phpstan-return array<Task>
     */
    public function findByUserPage(User $user, int $page = 1, int $limit = 10): array
    {
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
     * @param User $user
     * @return Task[]
     */
    public function findByUserAll(User $user): array
    {
        /** @var Task[] $tasks */
        $tasks = $this->em
            ->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->where('t.user = :userId')
            ->setParameter('userId', $user->getId())
            ->orderBy('t.id', 'ASC')
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
        return $this->em->getRepository(Task::class)->find($id);
    }

    /**
     * @param User $user
     * @param string|null $title
     * @param list<string>|null $status
     * @return Task[]
     */
    public function searchByUser(User $user, ?string $title, ?array $status): array
    {
        $em = $this->em;
        $qb = $em->createQueryBuilder();
        $qb->select('t')
            ->from(Task::class, 't')
            ->where('t.user = :userId')
            ->setParameter('userId', $user->getId());

        if ($title) {
            $qb->andWhere('t.title LIKE :title')
                ->setParameter('title', "%$title%");
        }

        if ($status) {
            $qb->andWhere('t.status IN (:status)')
                ->setParameter('status', $status);
        }

        /** @var Task[] $tasks */
        $tasks = $qb->getQuery()->getResult();

        return $tasks;
    }
}