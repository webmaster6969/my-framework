<?php

declare(strict_types=1);

namespace App\domain\Task\Presentation\HTTP;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Application\UseCases\Queries\FindUserQuery;
use App\domain\Task\Application\Repositories\TaskRepository;
use App\domain\Task\Application\UseCases\Commands\StoreTaskCommand;
use App\domain\Task\Application\UseCases\Commands\UserTaskCommand;
use App\domain\Task\Domain\Exceptions\NotCreateTaskException;
use App\domain\Task\Domain\Model\Entities\Task;
use Core\Http\Request;
use Core\Support\Session\Session;
use Core\View\View;
use DateTime;
use Exception;

class TaskController
{
    /**
     * @throws Exception
     */
    public function index()
    {
        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();

        $userTaskCommand = new UserTaskCommand(new TaskRepository(), $user, 1);
        $tasks = $userTaskCommand->execute();

        $view = new View();

        echo $view->render('tasks.index', ['tasks' => $tasks]);
    }

    /**
     * @throws Exception
     */
    public function create()
    {
        $view = new View();
        echo $view->render('tasks.create');
    }

    public function store()
    {
        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();

        $title = Request::input('title');
        $description = Request::input('description');
        $start_task = Request::input('start_task');
        $end_task = Request::input('end_task');

        $start_task_format_datetime = DateTime::createFromFormat('Y-m-d\TH:i:s', $start_task);
        $end_task_format_datetime = DateTime::createFromFormat('Y-m-d\TH:i:s', $end_task);

        $task = new Task($user, $title, $description, $start_task_format_datetime, $end_task_format_datetime);
        $findUserQuery = new StoreTaskCommand(new TaskRepository(), $task);
        if (!$findUserQuery->execute()) {
            throw new NotCreateTaskException('Task not created');
        }

        header('Location: /tasks');
        exit;
    }
}
