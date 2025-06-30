<?php

declare(strict_types=1);

namespace App\domain\Task\Domain\Repositories;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Task\Domain\Model\Entities\Task;
use DateTimeInterface;

interface TaskRepositoryInterface
{
    /**
     * @param Task $task
     * @return bool
     */
    public function save(Task $task): bool;

    /**
     * @param Task $task
     * @return bool
     */
    public function delete(Task $task): bool;

    /**
     * @param Task $task
     * @return bool
     */
    public function update(Task $task): bool;

    /**
     * @param int $id
     * @return Task|null
     */
    public function find(int $id): ?Task;

    /** @return list<Task> */
    public function findAll(): array;

    /**
     * @param User $user
     * @param int $task_id
     * @return Task|null
     */
    public function findByUser(User $user, int $task_id): ?Task;

    /**
     * @param User $user
     * @param string|null $title
     * @param list<string>|null $status
     * @return list<Task>
     */
    public function findTitleAndStatusByUser(User $user, ?string $title, ?array $status): array;

    /** @return list<Task> */
    public function findByUserAll(User $user): array;

    /**
     * @param DateTimeInterface $endTask
     * @param list<string> $status
     * @return Task[]
     */
    public function findEndTaskAndStatus(DateTimeInterface $endTask, array $status): array;

    /**
     * @return list<Task>
     */
    public function findByUserPage(
        User $user,
        int  $page = 1,
        int  $limit = 10
    ): array;
}