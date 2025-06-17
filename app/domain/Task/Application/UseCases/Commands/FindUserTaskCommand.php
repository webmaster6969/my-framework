<?php

declare(strict_types=1);

namespace App\domain\Task\Application\UseCases\Commands;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Common\Domain\CommandInterface;
use App\domain\Task\Application\Repositories\TaskRepository;
use App\domain\Task\Domain\Model\Entities\Task;

class FindUserTaskCommand implements CommandInterface
{
    /**
     * @param TaskRepository $taskRepository
     * @param User $user
     * @param int $task_id
     */
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly User           $user,
        private readonly int            $task_id
    )
    {
    }

    /**
     * @return Task|null
     */
    public function execute(): Task|null
    {
        return $this->taskRepository->findByUser($this->user, $this->task_id);
    }
}