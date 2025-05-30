<?php

declare(strict_types=1);

namespace App\domain\Task\Application\UseCases\Commands;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Common\Domain\CommandInterface;
use App\domain\Task\Application\Repositories\TaskRepository;
use App\domain\Task\Domain\Exceptions\NotYourTaskException;
use App\domain\Task\Domain\Model\Entities\Task;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class DeleteTaskCommand implements CommandInterface
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly User           $user,
        private readonly Task           $task,
    )
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function execute(): bool
    {
        if ($this->task->getUser()->getId() !== $this->user->getId()) {
            throw new NotYourTaskException('Not your task');
        }
        return $this->taskRepository->delete($this->task);
    }
}