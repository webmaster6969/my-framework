<?php

declare(strict_types=1);

namespace App\domain\Task\Application\UseCases\Commands;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Common\Domain\CommandInterface;
use App\domain\Task\Application\Repositories\TaskRepository;
use App\domain\Task\Domain\Model\Entities\Task;

class SearchUserTaskCommand implements CommandInterface
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
        private readonly ?array         $status,   // ← тип “array”, но PHPDoc уточняет «list<string>|null»
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
            ->searchByUser($this->user, $this->title, $this->status);

        return $tasks;
    }
}