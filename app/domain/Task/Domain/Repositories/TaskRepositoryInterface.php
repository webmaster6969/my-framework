<?php

declare(strict_types=1);

namespace App\domain\Task\Domain\Repositories;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Task\Domain\Model\Entities\Task;

interface TaskRepositoryInterface
{
    public function save(Task $task): bool;
    public function delete($task);
    public function update($task);
    public function find($id);
    public function findAll();
    public function findByUser(User $user, int $page = 1, int $limit = 10): array;
}