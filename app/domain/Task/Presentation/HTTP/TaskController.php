<?php

declare(strict_types=1);

namespace App\domain\Task\Presentation\HTTP;

use App\domain\Auth\Services\AuthService;
use App\domain\Common\Domain\Exceptions\ClearCacheException;
use App\domain\Task\Application\Repositories\TaskRepository;
use App\domain\Task\Application\UseCases\Commands\{DeleteTaskCommand,
    FindUserTaskCommand,
    StoreTaskCommand,
    UpdateTaskCommand,
    UserTaskCommand};
use App\domain\Task\Domain\Exceptions\{NotCreateTaskException, NotDeleteTaskException};
use App\domain\Task\Domain\Model\Entities\Task;
use Core\{Cache\Cache,
    Http\Request,
    Response\Response,
    Routing\Redirect,
    Support\Session\Session,
    Validator\Validator,
    View\View};
use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class TaskController
{
    /**
     * @param string $input
     * @return DateTime|null
     */
    private function parseDate(string $input): ?DateTime
    {
        $date = DateTime::createFromFormat('Y-m-d\TH:i:s', $input)
            ?: DateTime::createFromFormat('Y-m-d\TH:i', $input);

        return $date ?: null;
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $user = AuthService::getUser();
        if (!$user) return Response::make(Redirect::to('/login'));

        $cache = new Cache();
        $key = 'tasks_' . $user->getId();
        $tasks = $cache->get($key) ?? new UserTaskCommand(new TaskRepository(), $user, 1)->execute();
        $cache->set($key, $tasks, 10);

        return Response::make(new View('tasks.index', compact('tasks')))
            ->withHeaders(['Content-Type' => 'text/html'])->withStatus(200);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        $data = Session::flash('data');
        $data = is_array($data) ? $data : [];
        $defaults = ['title' => '', 'description' => '', 'start_task' => '', 'end_task' => ''];
        $data = array_merge($defaults, $data);

        return Response::make(new View('tasks.create', ['data' => $data, 'errors' => Session::error()]))
            ->withHeaders(['Content-Type' => 'text/html'])->withStatus(200);
    }

    /**
     * @return void
     */
    public function clearCache(): void
    {
        $user = AuthService::getUser();
        if (!$user) throw new ClearCacheException('Clear cache: User not found');

        new Cache()->delete('tasks_' . $user->getId());
    }

    /**
     * @throws NotCreateTaskException|ORMException|OptimisticLockException
     */
    public function store(): Response
    {
        $user = AuthService::getUser();
        if (!$user) return Response::make(Redirect::to('/login'));

        $data = Request::only(['title', 'description', 'start_task', 'end_task']);

        $validator = new Validator($data, [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:3|max:255',
            'start_task' => 'required|date_format:Y-m-d\TH:i:s',
            'end_task' => 'required|date_format:Y-m-d\TH:i:s',
        ]);

        if ($validator->fails()) {
            return Response::make(
                Redirect::to('/tasks/create')
                    ->with('data', $data)
                    ->withErrors($validator->errors())
            );
        }

        $start = isset($data['start_task']) && is_string($data['start_task']) ? $this->parseDate($data['start_task']) : null;
        $end = isset($data['end_task']) && is_string($data['end_task']) ? $this->parseDate($data['end_task']) : null;

        if (!$start || !$end) throw new NotCreateTaskException('Invalid datetime format');

        $title = is_string($data['title']) ? $data['title'] : '';
        $description = is_string($data['description']) ? $data['description'] : '';

        $task = new Task($user, $title, $description, $start, $end);
        if (!new StoreTaskCommand(new TaskRepository(), $task)->execute()) {
            throw new NotCreateTaskException('Task not created');
        }

        $this->clearCache();
        return Response::make(Redirect::to('/tasks'));
    }

    /**
     * @return Response
     */
    public function edit(): Response
    {
        $user = AuthService::getUser();
        if (!$user) return Response::make(Redirect::to('/login'));

        $id = Request::input('id');
        if (!is_numeric($id)) return Response::make(Redirect::to('/tasks'));

        $task = new FindUserTaskCommand(new TaskRepository(), $user, (int)$id)->execute();
        if (!$task) return Response::make(Redirect::to('/tasks'));

        return Response::make(new View('tasks.edit', ['task' => $task, 'errors' => Session::error()]))
            ->withHeaders(['Content-Type' => 'text/html'])->withStatus(200);
    }

    /**
     * @throws NotCreateTaskException|ORMException|OptimisticLockException
     */
    public function update(): Response
    {
        $user = AuthService::getUser();
        if (!$user) return Response::make(Redirect::to('/login'));

        $id = Request::input('id');
        if (!is_numeric($id)) return Response::make(Redirect::to('/tasks'));

        $data = Request::only(['title', 'description', 'start_task', 'end_task']);

        $validator = new Validator($data, [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:3|max:255',
            'start_task' => 'required|date_format:Y-m-d\TH:i:s',
            'end_task' => 'required|date_format:Y-m-d\TH:i:s',
        ]);

        if ($validator->fails()) {
            return Response::make(
                Redirect::to("/tasks/edit/?id=$id")
                    ->with('data', $data)
                    ->withErrors($validator->errors())
            );
        }

        $start = isset($data['start_task']) && is_string($data['start_task']) ? $this->parseDate($data['start_task']) : null;
        $end = isset($data['end_task']) && is_string($data['end_task']) ? $this->parseDate($data['end_task']) : null;

        if (!$start || !$end) throw new NotCreateTaskException('Invalid datetime format');

        $task = new FindUserTaskCommand(new TaskRepository(), $user, (int)$id)->execute();
        if (!$task) return Response::make(Redirect::to('/tasks'));

        $title = is_string($data['title']) ? $data['title'] : '';
        $description = is_string($data['description']) ? $data['description'] : '';

        $task->setTitle($title);
        $task->setDescription($description);
        $task->setStartTask($start);
        $task->setEndTask($end);

        if (!new UpdateTaskCommand(new TaskRepository(), $user, $task)->execute()) {
            throw new NotCreateTaskException('Task not updated');
        }

        $this->clearCache();
        return Response::make(Redirect::to('/tasks'));
    }

    /**
     * @throws NotDeleteTaskException|ORMException|OptimisticLockException
     */
    public function delete(): Response
    {
        $user = AuthService::getUser();
        if (!$user) return Response::make(Redirect::to('/login'));

        $id = Request::input('id');
        if (!is_numeric($id)) return Response::make(Redirect::to('/tasks'));

        $task = new FindUserTaskCommand(new TaskRepository(), $user, (int)$id)->execute();
        if (!$task) return Response::make(Redirect::to('/tasks'));

        if (!new DeleteTaskCommand(new TaskRepository(), $user, $task)->execute()) {
            throw new NotDeleteTaskException('Task not deleted');
        }

        $this->clearCache();
        return Response::make(Redirect::to('/tasks'));
    }
}