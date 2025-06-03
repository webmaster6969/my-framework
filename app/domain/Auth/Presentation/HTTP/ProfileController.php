<?php

declare(strict_types=1);

namespace App\domain\Auth\Presentation\HTTP;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Application\UseCases\Commands\UpdateUserCommand;
use App\domain\Auth\Application\UseCases\Queries\FindUserQuery;
use Core\Http\Request;
use Core\Response\Response;
use Core\Routing\Redirect;
use Core\Support\Session\Session;
use Core\Validator\Validator;
use Core\View\View;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;

class ProfileController
{
    /**
     * @throws Exception
     */
    public function index(): Response
    {
        $userId = Session::get('user_id');
        if (!is_int($userId)) {
            // если в сессии нет корректного id — редирект на логин
            return Response::make(Redirect::to('/login'));
        }

        $user = new FindUserQuery(new UserRepositories(), $userId)->handle();

        if ($user === null) {
            return Response::make(Redirect::to('/login'));
        }

        $view = new View('auth.profile', ['user' => $user, 'errors' => Session::error()]);

        return Response::make($view)->withHeaders([
            'Content-Type' => 'text/html',
        ])->withStatus(200);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update(): Response
    {
        $userId = Session::get('user_id');
        if (!is_int($userId)) {
            return Response::make(Redirect::to('/login'));
        }

        $name = Request::input('name');

        $data = ['name' => $name];
        $rules = ['name' => 'required|min:3|max:255'];

        $validator = new Validator($data, $rules);
        if ($validator->fails()) {
            Redirect::to('/profile')
                ->with('data', $data)
                ->withErrors($validator->errors())
                ->send();
        }

        $user = new FindUserQuery(new UserRepositories(), $userId)->handle();

        if ($user === null) {
            return Response::make(Redirect::to('/login'));
        }

        if (!is_string($name)) {
            return Response::make(Redirect::to('/profile'));
        }

        $user->setName($name);
        $user = new UpdateUserCommand(new UserRepositories(), $user)->execute();

        return Response::make(Redirect::to('/profile'));
    }
}