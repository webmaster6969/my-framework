<?php

declare(strict_types=1);

namespace App\domain\Task\Domain\Repositories;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Task\Domain\Model\Entities\Task;

interface TaskRepositoryInterface
{
    public function save(Task $task): bool;

    public function delete(Task $task): bool;

    public function update(Task $task): bool;

    public function find($id);

    public function findAll();

    public function findByUser(User $user, int $task_id): ?Task;

    public function findByUserAll(User $user, int $page = 1, int $limit = 10): array;
}