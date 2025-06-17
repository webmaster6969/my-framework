<?php

declare(strict_types=1);

namespace App\domain\Task\Application\UseCases\Commands;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Common\Domain\CommandInterface;
use App\domain\Task\Application\Repositories\TaskRepository;
use App\domain\Task\Domain\Model\Entities\Task;

class UserTaskAllCommand implements CommandInterface
{
    /**
     * @param TaskRepository $taskRepository
     * @param User $user
     */
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly User           $user,
    ) {}

    /**
     * @return Task[]
     */
    public function execute(): array
    {
        return $this->taskRepository->findByUserAll($this->user);
    }
}