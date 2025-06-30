<?php

namespace App\domain\Task\Application\ConsoleCommand;

use App\domain\Task\Application\Repositories\TaskRepository;
use App\domain\Task\Application\UseCases\Commands\FindStatusAndEndTaskCommand;
use App\domain\Task\Domain\Model\Entities\Task;
use Core\Console\Command;
use Core\Database\DB;

class CheckEndTask extends Command
{
    /**
     * @var string
     */
    protected string $name = 'checkEndTask';
    /**
     * @var string
     */
    protected string $description = 'Check end task';

    /**
     * @param list<string> $arguments
     * @return void
     */
    public function handle(array $arguments): void
    {
        $findStatusAndEndTaskCommand = new FindStatusAndEndTaskCommand(new TaskRepository(DB::getEntityManager()),
            new \DateTimeImmutable(), [
                Task::STATUS_IN_PROGRESS,
                Task::STATUS_PENDING
            ]);
        $tasks = $findStatusAndEndTaskCommand->execute();

        foreach ($tasks as $task) {
            echo $task->getId() . ' ' . $task->getTitle() . ' ' . $task->getStatus() . PHP_EOL;
        }
    }
}