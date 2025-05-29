<?php

declare(strict_types=1);

namespace App\domain\Task\Application\UseCases\Commands;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Common\Domain\CommandInterface;
use App\domain\Task\Application\Repositories\TaskRepository;

class UserTaskCommand implements CommandInterface
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly User           $user,
        private readonly int            $page
    )
    {
    }

    public function execute(): array
    {
        return $this->taskRepository->findByUser($this->user, $this->page);
    }
}