<?php

declare(strict_types=1);

namespace App\domain\Task\Presentation\HTTP;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Application\UseCases\Queries\FindUserQuery;
use App\domain\Common\Domain\Exceptions\ClearCacheException;
use App\domain\Task\Application\Repositories\TaskRepository;
use App\domain\Task\Application\UseCases\Commands\DeleteTaskCommand;
use App\domain\Task\Application\UseCases\Commands\FindUserTaskCommand;
use App\domain\Task\Application\UseCases\Commands\StoreTaskCommand;
use App\domain\Task\Application\UseCases\Commands\UpdateTaskCommand;
use App\domain\Task\Application\UseCases\Commands\UserTaskCommand;
use App\domain\Task\Domain\Exceptions\NotCreateTaskException;
use App\domain\Task\Domain\Exceptions\NotDeleteTaskException;
use App\domain\Task\Domain\Model\Entities\Task;
use App\domain\Auth\Domain\Model\Entities\User;
use Core\Cache\Cache;
use Core\Http\Request;
use Core\Response\Response;
use Core\Routing\Redirect;
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
    public function index(): Response
    {
        $userId = Session::get('user_id');
        if (!is_int($userId)) {
            return Response::make(Redirect::to('/login'));
        }

        $user = new FindUserQuery(new UserRepositories(), $userId)->handle();
        if (!$user instanceof User) {
            return Response::make(Redirect::to('/login'));
        }

        $cache = new Cache();
        if ($cache->has('tasks_' . $user->getId())) {
            $tasks = $cache->get('tasks_' . $user->getId());
        }

        if (empty($tasks)){
            $tasks = new UserTaskCommand(new TaskRepository(), $user, 1)->execute();
            $cache->set('tasks_' . $user->getId(), $tasks, 10);
        }

        return Response::make(new View('tasks.index', ['tasks' => $tasks]))
            ->withHeaders(['Content-Type' => 'text/html'])
            ->withStatus(200);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        $dataFlash = Session::flash('data');
        $data = is_array($dataFlash) ? [
            'title' => $dataFlash['title'] ?? '',
            'description' => $dataFlash['description'] ?? '',
            'start_task' => $dataFlash['start_task'] ?? '',
            'end_task' => $dataFlash['end_task'] ?? '',
        ] : ['title' => '', 'description' => '', 'start_task' => '', 'end_task' => ''];

        return Response::make(new View('tasks.create', ['data' => $data, 'errors' => Session::error()]))
            ->withHeaders(['Content-Type' => 'text/html'])
            ->withStatus(200);
    }

    public function clearCache(): void
    {
        $userId = Session::get('user_id');
        if (!is_int($userId) && !is_null($userId)) {
            $userId = is_numeric($userId) ? (int)$userId : null;
        }

        $findUserQuery = new FindUserQuery(new UserRepositories(), $userId);

        $user = $findUserQuery->handle();

        if (!$user instanceof User) {
            throw new ClearCacheException('Clear cache: User not found');
        }

        $cache = new Cache();
        $cache->delete('tasks_' . $user->getId());
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws NotCreateTaskException
     */
    public function store(): Response
    {
        $userId = Session::get('user_id');
        if (!is_int($userId)) return Response::make(Redirect::to('/login'));

        $user = new FindUserQuery(new UserRepositories(), $userId)->handle();
        if (!$user instanceof User) return Response::make(Redirect::to('/login'));

        $titleInput = Request::input('title');
        $descriptionInput = Request::input('description');
        $start_task = Request::input('start_task');
        $end_task = Request::input('end_task');

        $title = is_string($titleInput) ? $titleInput : '';
        $description = is_string($descriptionInput) ? $descriptionInput : '';

        $start_task = is_string($start_task) ? $start_task : '';
        $end_task = is_string($end_task) ? $end_task : '';

        $data = compact('title', 'description', 'start_task', 'end_task');

        $validator = new Validator($data, [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:3|max:255',
            'start_task' => 'required|date_format:Y-m-d\TH:i:s',
            'end_task' => 'required|date_format:Y-m-d\TH:i:s',
        ]);

        if ($validator->fails()) {
            return Response::make(Redirect::to('/tasks/create')->with('data', $data)->withErrors($validator->errors()));
        }

        $startDate = DateTime::createFromFormat('Y-m-d\TH:i:s', $start_task);
        if (!$startDate) {
            $startDate = DateTime::createFromFormat('Y-m-d\TH:i', $start_task);
        }

        $endDate = DateTime::createFromFormat('Y-m-d\TH:i:s', $end_task);
        if (!$endDate) {
            $endDate = DateTime::createFromFormat('Y-m-d\TH:i', $end_task);
        }

        if (!$startDate || !$endDate) {
            throw new NotCreateTaskException('Invalid datetime format.');
        }

        $task = new Task($user, $title, $description, $startDate, $endDate);
        if (!new StoreTaskCommand(new TaskRepository(), $task)->execute()) {
            throw new NotCreateTaskException('Task not created');
        }

        $this->clearCache();

        return Response::make(Redirect::to('/tasks'));
    }

    /**
     * @throws Exception
     */
    public function edit(): Response
    {
        $task_id = Request::input('id');
        if (!is_numeric($task_id)) return Response::make(Redirect::to('/tasks'));

        $userId = Session::get('user_id');
        if (!is_int($userId)) return Response::make(Redirect::to('/login'));

        $user = new FindUserQuery(new UserRepositories(), $userId)->handle();
        if (!$user instanceof User) return Response::make(Redirect::to('/login'));

        $task = new FindUserTaskCommand(new TaskRepository(), $user, (int)$task_id)->execute();

        if (!$task) return Response::make(Redirect::to('/tasks'));

        return Response::make(new View('tasks.edit', ['task' => $task, 'errors' => Session::error()]))
            ->withHeaders(['Content-Type' => 'text/html'])
            ->withStatus(200);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NotCreateTaskException
     */
    public function update(): Response
    {
        $userId = Session::get('user_id');
        if (!is_int($userId)) return Response::make(Redirect::to('/login'));

        $user = new FindUserQuery(new UserRepositories(), $userId)->handle();
        if (!$user instanceof User) return Response::make(Redirect::to('/login'));

        $task_id = Request::input('id');
        if (!is_numeric($task_id)) return Response::make(Redirect::to('/tasks'));

        $titleInput = Request::input('title');
        $descriptionInput = Request::input('description');
        $start_task = Request::input('start_task');
        $end_task = Request::input('end_task');

        $title = is_string($titleInput) ? $titleInput : '';
        $description = is_string($descriptionInput) ? $descriptionInput : '';

        $start_task = is_string($start_task) ? $start_task : '';
        $end_task = is_string($end_task) ? $end_task : '';

        $data = [
            'title' => $title,
            'description' => $description,
            'start_task' => $start_task,
            'end_task' => $end_task,
        ];

        $validator = new Validator($data, [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:3|max:255',
            'start_task' => 'required|date_format:Y-m-d\TH:i:s',
            'end_task' => 'required|date_format:Y-m-d\TH:i:s',
        ]);

        if ($validator->fails()) {
            return Response::make(Redirect::to('/tasks/edit/?id=' . (int)$task_id)
                ->with('data', $data)
                ->withErrors($validator->errors()));
        }

        $startDate = DateTime::createFromFormat('Y-m-d\TH:i:s', $start_task);
        if (!$startDate) {
            $startDate = DateTime::createFromFormat('Y-m-d\TH:i', $start_task);
        }

        $endDate = DateTime::createFromFormat('Y-m-d\TH:i:s', $end_task);
        if (!$endDate) {
            $endDate = DateTime::createFromFormat('Y-m-d\TH:i', $end_task);
        }

        if (!$startDate || !$endDate) {
            throw new NotCreateTaskException('Invalid datetime format.');
        }

        $task = new FindUserTaskCommand(new TaskRepository(), $user, (int)$task_id)->execute();
        if (!$task) return Response::make(Redirect::to('/tasks'));

        $task->setTitle($data['title']);
        $task->setDescription($data['description']);
        $task->setStartTask($startDate);
        $task->setEndTask($endDate);

        if (!new UpdateTaskCommand(new TaskRepository(), $user, $task)->execute()) {
            throw new NotCreateTaskException('Task not updated');
        }

        $this->clearCache();

        return Response::make(Redirect::to('/tasks'));
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NotDeleteTaskException
     */
    public function delete(): Response
    {
        $userId = Session::get('user_id');
        if (!is_int($userId)) return Response::make(Redirect::to('/login'));

        $user = new FindUserQuery(new UserRepositories(), $userId)->handle();
        if (!$user instanceof User) return Response::make(Redirect::to('/login'));

        $task_id = Request::input('id');
        if (!is_numeric($task_id)) return Response::make(Redirect::to('/tasks'));

        $task = new FindUserTaskCommand(new TaskRepository(), $user, (int)$task_id)->execute();
        if (!$task) return Response::make(Redirect::to('/tasks'));

        if (!new DeleteTaskCommand(new TaskRepository(), $user, $task)->execute()) {
            throw new NotDeleteTaskException('Task not deleted');
        }

        $this->clearCache();

        return Response::make(Redirect::to('/tasks'));
    }
}