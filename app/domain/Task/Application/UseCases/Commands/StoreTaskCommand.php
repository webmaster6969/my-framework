<?php

declare(strict_types=1);

namespace App\domain\Task\Application\UseCases\Commands;

use App\domain\Common\Domain\CommandInterface;
use App\domain\Task\Application\Repositories\TaskRepository;
use App\domain\Task\Domain\Model\Entities\Task;

class StoreTaskCommand implements CommandInterface
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly Task           $task,
    )
    {
    }

    public function execute(): true
    {
        return $this->taskRepository->save($this->task);
    }
}