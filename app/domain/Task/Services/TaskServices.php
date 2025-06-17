<?php

namespace App\domain\Task\Services;

use App\domain\Auth\Domain\Exceptions\UserNotFoundException;
use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Auth\Services\AuthService;
use App\domain\Common\Domain\Exceptions\EncryptionKeyIsNotFindException;
use App\domain\Task\Application\Repositories\TaskRepository;
use App\domain\Task\Application\UseCases\Commands\UpdateTaskCommand;
use App\domain\Task\Application\UseCases\Commands\UserTaskAllCommand;
use App\domain\Task\Domain\Exceptions\NotCreateTaskException;
use App\domain\Task\Domain\Model\Entities\Task;
use Core\Database\DB;
use Core\Support\Crypt\Crypt;
use Core\Support\Env\Env;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Mockery\Exception;

class TaskServices
{
    /**
     * @param User $user
     * @param string $oldEncryptionKey
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public static function reEncryptDescription(User $user, string $oldEncryptionKey): bool
    {
        $command = new UserTaskAllCommand(new TaskRepository(DB::getEntityManager()), $user);
        $tasks = $command->execute();

        foreach ($tasks as $task) {
            $description = $task->getDescription();
            if (empty($description)) {
                continue;
            }
            $description = Crypt::decrypt($task->getDescription() ?? '', $oldEncryptionKey);
            $task->setDescription($description);
            self::EncryptDescription($task);
            $updateCommand = new UpdateTaskCommand(new TaskRepository(DB::getEntityManager()), $user, $task);
            $updateCommandExecute = $updateCommand->execute();

            if (!$updateCommandExecute) {
                throw new NotCreateTaskException('Task not updated');
            }
        }

        return true;
    }

    /**
     * @param Task $task
     * @return void
     */
    public static function EncryptDescription(Task $task): void
    {
        $user = AuthService::getUser();

        if ($user !== null && !empty($user->getEncryptionKey())) {
            $encryptionKey = $user->getEncryptionKey();
        } else {
            $encryptionKey = Env::get('ENCRYPTION_KEY');
        }

        if (empty($encryptionKey) || !is_string($encryptionKey)) {
            throw new EncryptionKeyIsNotFindException('ENCRYPTION_KEY environment variable is not set');
        }

        $description = $task->getDescription();
        if (!is_string($description) || empty($description)) {
            throw new \InvalidArgumentException('Task description must be a string');
        }

        $task->setDescription(Crypt::encrypt($description, $encryptionKey));
    }

    /**
     * @param Task $task
     * @return string
     */
    public static function DecryptDescription(Task $task): string
    {
        $user = AuthService::getUser();

        if (empty($user)) {
            throw new UserNotFoundException('User not found');
        }

        $encryptionKey = !empty($user->getEncryptionKey()) ? $user->getEncryptionKey() : Env::get('ENCRYPTION_KEY');
        if (empty($encryptionKey) || !is_string($encryptionKey)) {
            throw new EncryptionKeyIsNotFindException('ENCRYPTION_KEY environment variable is not set');
        }

        return Crypt::decrypt($task->getDescription() ?? '', $encryptionKey);
    }
}