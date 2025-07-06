<?php

declare(strict_types=1);

namespace App\domain\Task\Application\UseCases\Queries;

use App\domain\Common\Domain\CommandInterface;
use App\domain\Task\Application\Repositories\TaskRepository;
use App\domain\Task\Domain\Model\Entities\Task;
use DateTimeInterface;

class FindStatusAndEndTaskCommand implements CommandInterface
{
    /**
     * @param TaskRepository $taskRepository
     * @param DateTimeInterface $endTask
     * @param list<string>|null $status
     */
    public function __construct(
        private readonly TaskRepository    $taskRepository,
        private readonly DateTimeInterface $endTask,
        private readonly ?array            $status,
    )
    {
    }

    /**
     * @return list<Task>
     */
    public function execute(): array
    {
        /** @var list<string> $status */
        $status = $this->status ?? [];

        /** @var list<Task> $tasks */
        $tasks = $this->taskRepository->findEndTaskAndStatus($this->endTask, $status);

        return $tasks;
    }
}