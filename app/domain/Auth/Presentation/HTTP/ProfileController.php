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
        $user = new FindUserQuery(new UserRepositories(), Session::get('user_id'))->handle();

        if (empty($user)) {
            Redirect::to('/login')->send();
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
    public function update()
    {
        $name = Request::input('name');

        $data = [
            'name' => $name,
        ];

        $rules = [
            'name' => 'required|min:3|max:255',
        ];

        $validator = new Validator($data, $rules);
        if ($validator->fails()) {
            Redirect::to('/profile')
                ->with('data', $data)
                ->withErrors($validator->errors())
                ->send();
        }

        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();
        $user->setName($name);
        $updateUserCommand = new UpdateUserCommand(new UserRepositories(), $user);
        $user = $updateUserCommand->execute();

        Redirect::to('/profile')->send();
    }
}
