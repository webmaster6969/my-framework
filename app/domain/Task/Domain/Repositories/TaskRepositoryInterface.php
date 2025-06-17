<?php

declare(strict_types=1);

namespace App\domain\Task\Domain\Repositories;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Task\Domain\Model\Entities\Task;

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

    /**
     * @return Task[]
     */
    public function findAll(): array;

    /**
     * @param User $user
     * @param int $task_id
     * @return mixed
     */
    public function findByUser(User $user, int $task_id): mixed;

    /**
     * @param User $user
     * @return mixed
     */
    public function findByUserAll(User $user): mixed;

    /**
     * @param User $user
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function findByUserPage(User $user, int $page = 1, int $limit = 10): mixed;
}