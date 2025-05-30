<?php

declare(strict_types=1);

namespace App\domain\Task\Presentation\HTTP;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Application\UseCases\Queries\FindUserQuery;
use App\domain\Common\Domain\Exceptions\CsrfException;
use App\domain\Task\Application\Repositories\TaskRepository;
use App\domain\Task\Application\UseCases\Commands\DeleteTaskCommand;
use App\domain\Task\Application\UseCases\Commands\FindUserTaskCommand;
use App\domain\Task\Application\UseCases\Commands\StoreTaskCommand;
use App\domain\Task\Application\UseCases\Commands\UpdateTaskCommand;
use App\domain\Task\Application\UseCases\Commands\UserTaskCommand;
use App\domain\Task\Domain\Exceptions\NotCreateTaskException;
use App\domain\Task\Domain\Exceptions\NotDeleteTaskException;
use App\domain\Task\Domain\Model\Entities\Task;
use Core\Http\Request;
use Core\Routing\Redirect;
use Core\Support\Csrf\Csrf;
use Core\Support\Session\Session;
use Core\Validator\Validator;
use Core\View\View;
use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
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
        $dataFlash = Session::flash('data');
        $data = [
            'title' => $dataFlash['title'] ?? '',
            'description' => $dataFlash['description'] ?? '',
            'start_task' => $dataFlash['start_task'] ?? '',
            'end_task' => $dataFlash['end_task'] ?? '',
        ];

        $view = new View();
        echo $view->render('tasks.create', ['data' => $data, 'errors' => Session::error()]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function store()
    {
        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();

        $title = Request::input('title');
        $description = Request::input('description');
        $start_task = Request::input('start_task');
        $end_task = Request::input('end_task');
        $csrfToken = Request::input('csrf_token');

        if (!Csrf::check($csrfToken)) {
            throw new CsrfException('Csrf error');
        }

        $data = [
            'title' => $title,
            'description' => $description,
            'start_task' => $start_task,
            'end_task' => $end_task
        ];

        $rules = [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:3|max:255',
            'start_task' => 'required|date_format:Y-m-d\TH:i:s',
            'end_task' => 'required|date_format:Y-m-d\TH:i:s',
        ];

        $validator = new Validator($data, $rules);

        if ($validator->fails()) {
            Redirect::to('/tasks/create')
                ->with('data', $data)
                ->withErrors($validator->errors())
                ->send();
        }

        $start_task_format_datetime = DateTime::createFromFormat('Y-m-d\TH:i:s', $start_task);
        $end_task_format_datetime = DateTime::createFromFormat('Y-m-d\TH:i:s', $end_task);

        $task = new Task($user, $title, $description, $start_task_format_datetime, $end_task_format_datetime);
        $findUserQuery = new StoreTaskCommand(new TaskRepository(), $task);
        if (!$findUserQuery->execute()) {
            throw new NotCreateTaskException('Task not created');
        }

        Redirect::to('/tasks')->send();
        exit;
    }

    /**
     * @throws Exception
     */
    public function edit()
    {
        $task_id = (int)Request::input('id');

        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();

        $findUserQuery = new FindUserTaskCommand(new TaskRepository(), $user, $task_id);
        $task = $findUserQuery->execute();

        if (empty($task)) {
            Redirect::to('/tasks')->send();
            exit;
        }

        $view = new View();
        echo $view->render('tasks.edit', ['task' => $task, 'errors' => Session::error()]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update()
    {
        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();

        $title = Request::input('title');
        $description = Request::input('description');
        $start_task = Request::input('start_task');
        $end_task = Request::input('end_task');
        $task_id = (int)Request::input('id');
        $csrfToken = Request::input('csrf_token');

        if (!Csrf::check($csrfToken)) {
            throw new CsrfException('Csrf error');
        }

        $data = [
            'title' => $title,
            'description' => $description,
            'start_task' => $start_task,
            'end_task' => $end_task
        ];

        $rules = [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:3|max:255',
            'start_task' => 'required|date_format:Y-m-d\TH:i:s',
            'end_task' => 'required|date_format:Y-m-d\TH:i:s',
        ];

        $validator = new Validator($data, $rules);

        if ($validator->fails()) {
            Redirect::to('/tasks/edit/' . '?id=' . $task_id)
                ->with('data', $data)
                ->withErrors($validator->errors())
                ->send();
        }

        $start_task_format_datetime = DateTime::createFromFormat('Y-m-d\TH:i:s', $start_task);
        $end_task_format_datetime = DateTime::createFromFormat('Y-m-d\TH:i:s', $end_task);

        $findUserQuery = new FindUserTaskCommand(new TaskRepository(), $user, $task_id);
        $task = $findUserQuery->execute();

        if (empty($task)) {
            Redirect::to('/tasks')->send();
            exit;
        }

        $task->setTitle($title);
        $task->setDescription($description);
        $task->setStartTask($start_task_format_datetime);
        $task->setEndTask($end_task_format_datetime);
        $findUserQuery = new UpdateTaskCommand(new TaskRepository(), $user, $task);
        if (!$findUserQuery->execute()) {
            throw new NotCreateTaskException('Task not created');
        }

        Redirect::to('/tasks')->send();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function delete()
    {
        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();

        $task_id = (int)Request::input('id');

        $findUserQuery = new FindUserTaskCommand(new TaskRepository(), $user, $task_id);
        $task = $findUserQuery->execute();

        if (empty($task)) {
            Redirect::to('/tasks')->send();
        }

        $findUserQuery = new DeleteTaskCommand(new TaskRepository(), $user, $task);
        if (!$findUserQuery->execute()) {
            throw new NotDeleteTaskException('Task not deleted');
        }

        Redirect::to('/tasks')->send();
    }
}
