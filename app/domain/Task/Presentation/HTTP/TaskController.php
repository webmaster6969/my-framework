<?php

declare(strict_types=1);

namespace App\domain\Task\Presentation\HTTP;

use App\domain\Auth\Services\AuthService;
use App\domain\Common\Domain\Exceptions\ClearCacheException;
use App\domain\Task\Application\Repositories\TaskRepository;
use App\domain\Task\Services\TaskServices;
use App\domain\Task\Application\UseCases\Commands\{DeleteTaskCommand,
    FindTitleAndStatusByUserCommand,
    FindUserTaskCommand,
    StoreTaskCommand,
    UpdateTaskCommand,
    UserTaskPageCommand
};
use App\domain\Task\Domain\Exceptions\{
    NotCreateTaskException,
    NotDeleteTaskException
};
use App\domain\Task\Domain\Model\Entities\Task;
use Core\{Cache\Cache,
    Database\DB,
    Http\Request,
    Response\Response,
    Routing\Redirect,
    Support\App\App,
    Support\Session\Session,
    Validator\Validator,
    View\View
};
use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class TaskController
{
    /**
     * @param string|null $input
     * @return string|null
     */
    private function parseFormatDate(?string $input): ?string
    {
        if (empty($input)) {
            return null;
        }

        if (DateTime::createFromFormat('Y-m-d\TH:i', $input) !== false) {
            return 'Y-m-d\TH:i';
        }

        if (DateTime::createFromFormat('Y-m-d\TH:i:s', $input) !== false) {
            return 'Y-m-d\TH:i:s';
        }

        return null;
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $user = AuthService::getUser();
        if (empty($user)) {
            return Response::make(Redirect::to('/login'));
        }

        $cache = $this->getCache();
        $key = 'tasks_' . $user->getId();
        $tasks = $cache->get($key);

        if (empty($tasks)) {
            $command = new UserTaskPageCommand(new TaskRepository(DB::getEntityManager()), $user, 1);
            $tasks = $command->execute();
            $cache->set($key, $tasks, 10);
        }

        return Response::make(
            new View('tasks.index', compact('tasks'))->with('title', t('Tasks'))
        )->withHeaders(['Content-Type' => 'text/html'])->withStatus(200);
    }

    /**
     * @return Response
     */
    public function filterTasks(): Response
    {
        $user = AuthService::getUser();
        if (empty($user)) {
            return Response::make(Redirect::to('/login'));
        }

        /** @var array<string,mixed> $data */
        $data = Request::only(['search_title', 'search_status']);

        $searchTitle = is_string($data['search_title'] ?? null)
            ? $data['search_title']
            : '';

        /** @var list<string> $searchStatus */
        $searchStatus = isset($data['search_status']) && is_array($data['search_status'])
            ? array_values(array_filter($data['search_status'], 'is_string'))
            : [];

        $command = new FindTitleAndStatusByUserCommand(
            new TaskRepository(DB::getEntityManager()),
            $user,
            $searchTitle,
            $searchStatus
        );

        $tasks = $command->execute();

        return Response::make(
            new View('tasks.index', ['tasks' => $tasks, 'data' => $data])
                ->with('title', t('Tasks'))
        )->withHeaders(['Content-Type' => 'text/html'])
            ->withStatus(200);
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

        return Response::make(
            new View('tasks.create', ['data' => $data, 'errors' => Session::error()])->with('title', t('Create task'))
        )->withHeaders(['Content-Type' => 'text/html'])->withStatus(200);
    }

    /**
     * @return void
     */
    public function clearCache(): void
    {
        $user = AuthService::getUser();
        if (empty($user)) {
            throw new ClearCacheException('Clear cache: User not found');
        }

        $cache = $this->getCache();
        $cache->delete('tasks_' . $user->getId());
    }

    /**
     * @throws NotCreateTaskException|ORMException|OptimisticLockException
     */
    public function store(): Response
    {
        $user = AuthService::getUser();
        if (empty($user)) {
            return Response::make(Redirect::to('/login'));
        }

        /** @var array<string, mixed> $data */
        $data = Request::only(['title', 'description', 'start_task', 'status', 'end_task']);
        $title = isset($data['title']) && is_string($data['title']) ? $data['title'] : '';
        $description = isset($data['description']) && is_string($data['description']) ? $data['description'] : '';
        $status = isset($data['status']) && is_string($data['status']) ? $data['status'] : '';
        $startInput = isset($data['start_task']) && is_string($data['start_task']) ? $data['start_task'] : '';
        $endInput = isset($data['end_task']) && is_string($data['end_task']) ? $data['end_task'] : '';

        $formatStartTask = $this->parseFormatDate($startInput);
        $formatEndTask = $this->parseFormatDate($endInput);

        /** @var array<string, array<int, string>> $errors */
        $errors = [];

        if (empty($formatStartTask)) {
            $errors['start_task'][] = 'Invalid date format';
        }

        if (empty($formatEndTask)) {
            $errors['end_task'][] = 'Invalid date format';
        }

        if (!empty($errors)) {
            return Response::make(
                Redirect::to('/tasks/create')->with('data', $data)->withErrors($errors)
            );
        }

        $validator = new Validator(
            [
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'start_task' => $startInput,
                'end_task' => $endInput,
            ],
            [
                'title' => 'required|min:3|max:255',
                'description' => 'required|min:3|max:255',
                'status' => 'required|in:pending,in_progress,done,canceled',
                'start_task' => 'required|dateFormat:' . $formatStartTask,
                'end_task' => 'required|dateFormat:' . $formatEndTask,
            ]
        );

        if (!empty($validator->fails())) {
            return Response::make(
                Redirect::to('/tasks/create')->with('data', $data)->withErrors($validator->errors())
            );
        }

        $start = $formatStartTask ? DateTime::createFromFormat($formatStartTask, $startInput) : false;
        $end = $formatEndTask ? DateTime::createFromFormat($formatEndTask, $endInput) : false;

        if (empty($start) || empty($end)) {
            return Response::make(Redirect::to('/tasks/create')->withErrors([
                'start_task' => ['Failed to parse start date'],
                'end_task' => ['Failed to parse end date'],
            ]));
        }

        $task = new Task($user, $title, $description, $status, $start, $end);
        TaskServices::EncryptDescription($task);
        $command = new StoreTaskCommand(new TaskRepository(DB::getEntityManager()), $task);

        if (!$command->execute()) {
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
        if (empty($user)) {
            return Response::make(Redirect::to('/login'));
        }

        $id = Request::input('id');
        if (empty($id) || !is_numeric($id)) {
            return Response::make(Redirect::to('/tasks'));
        }

        $command = new FindUserTaskCommand(new TaskRepository(DB::getEntityManager()), $user, (int)$id);
        $task = $command->execute();

        if (!$task) {
            return Response::make(Redirect::to('/tasks'));
        }

        return Response::make(
            new View('tasks.edit', ['task' => $task, 'errors' => Session::error()])->with('title', t('Edit task'))
        )->withHeaders(['Content-Type' => 'text/html'])->withStatus(200);
    }

    /**
     * @throws NotCreateTaskException|ORMException|OptimisticLockException
     */
    public function update(): Response
    {
        $user = AuthService::getUser();
        if (empty($user)) {
            return Response::make(Redirect::to('/login'));
        }

        $id = Request::input('id');
        if (empty($id) || !is_numeric($id)) {
            return Response::make(Redirect::to('/tasks'));
        }

        /** @var array<string, mixed> $data */
        $data = Request::only(['title', 'description', 'status', 'start_task', 'end_task']);
        $title = isset($data['title']) && is_string($data['title']) ? $data['title'] : '';
        $description = isset($data['description']) && is_string($data['description']) ? $data['description'] : '';
        $status = isset($data['status']) && is_string($data['status']) ? $data['status'] : '';
        $startInput = isset($data['start_task']) && is_string($data['start_task']) ? $data['start_task'] : '';
        $endInput = isset($data['end_task']) && is_string($data['end_task']) ? $data['end_task'] : '';

        $formatStartTask = $this->parseFormatDate($startInput);
        $formatEndTask = $this->parseFormatDate($endInput);

        /** @var array<string, array<int, string>> $errors */
        $errors = [];

        if (empty($formatStartTask)) {
            $errors['start_task'][] = 'Invalid date format';
        }

        if (empty($formatEndTask)) {
            $errors['end_task'][] = 'Invalid date format';
        }

        if (!empty($errors)) {
            return Response::make(Redirect::to('/tasks/create')->with('data', $data)->withErrors($errors));
        }

        $validator = new Validator([
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'start_task' => $startInput,
            'end_task' => $endInput,
        ], [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:3|max:255',
            'status' => 'required|in:pending,in_progress,done,canceled',
            'start_task' => 'required|dateFormat:' . $formatStartTask,
            'end_task' => 'required|dateFormat:' . $formatEndTask,
        ]);

        if (!empty($validator->fails())) {
            return Response::make(
                Redirect::to("/tasks/edit/?id=$id")->with('data', $data)->withErrors($validator->errors())
            );
        }

        $start = $formatStartTask ? DateTime::createFromFormat($formatStartTask, $startInput) : false;
        $end = $formatEndTask ? DateTime::createFromFormat($formatEndTask, $endInput) : false;

        if (empty($start) || empty($end)) {
            return Response::make(Redirect::to("/tasks/edit/?id=$id")->withErrors([
                'start_task' => ['Failed to parse start date'],
                'end_task' => ['Failed to parse end date'],
            ]));
        }

        $taskCommand = new FindUserTaskCommand(new TaskRepository(DB::getEntityManager()), $user, (int)$id);
        $task = $taskCommand->execute();

        if (!$task) {
            return Response::make(Redirect::to('/tasks'));
        }

        $task->setTitle($title);
        $task->setDescription($description);
        TaskServices::EncryptDescription($task);
        $task->setStatus($status);
        $task->setStartTask($start);
        $task->setEndTask($end);

        $updateCommand = new UpdateTaskCommand(new TaskRepository(DB::getEntityManager()), $user, $task);
        $updateCommandExecute = $updateCommand->execute();

        if (!$updateCommandExecute) {
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
        if (empty($user)) {
            return Response::make(Redirect::to('/login'));
        }

        $id = Request::input('id');
        if (empty($id) || !is_numeric($id)) {
            return Response::make(Redirect::to('/tasks'));
        }

        $findCommand = new FindUserTaskCommand(new TaskRepository(DB::getEntityManager()), $user, (int)$id);
        $task = $findCommand->execute();

        if (empty($task)) {
            return Response::make(Redirect::to('/tasks'));
        }

        $deleteCommand = new DeleteTaskCommand(new TaskRepository(DB::getEntityManager()), $user, $task);
        $deleteCommandExecute = $deleteCommand->execute();

        if (!$deleteCommandExecute) {
            throw new NotDeleteTaskException('Task not deleted');
        }

        $this->clearCache();
        return Response::make(Redirect::to('/tasks'));
    }

    /**
     * @return Cache
     */
    private function getCache(): Cache
    {
        return new Cache(App::getBasePath() . DIRECTORY_SEPARATOR . 'storage/cache');
    }
}