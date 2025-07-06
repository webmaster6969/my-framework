<?php

declare(strict_types=1);

namespace App\domain\Task\Application\UseCases\Queries;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Common\Domain\CommandInterface;
use App\domain\Task\Application\Repositories\TaskRepository;
use App\domain\Task\Domain\Model\Entities\Task;

class FindTitleAndStatusByUserCommand implements CommandInterface
{
    /**
     * @param TaskRepository $taskRepository
     * @param User $user
     * @param string|null $title
     * @param list<string>|null $status
     */
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly User           $user,
        private readonly ?string        $title,
        private readonly ?array         $status,
    )
    {
    }

    /**
     * @return list<Task>
     */
    public function execute(): array
    {
        /** @var list<Task> $tasks */
        $tasks = $this->taskRepository
            ->findTitleAndStatusByUser($this->user, $this->title, $this->status);

        return $tasks;
    }
}