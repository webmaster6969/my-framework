<?php

declare(strict_types=1);

namespace App\domain\Auth\Presentation\HTTP;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Application\UseCases\Commands\LoginCommand;
use App\domain\Auth\Application\UseCases\Commands\LogoutCommand;
use App\domain\Auth\Application\UseCases\Commands\RegisterCommand;
use App\domain\Auth\Application\UseCases\Queries\FindUserQuery;
use App\domain\Auth\Domain\Exceptions\LogoutException;
use Core\Http\Request;
use Core\Response\Response;
use Core\Routing\Redirect;
use Core\Support\Session\Session;
use Core\Validator\Validator;
use Core\View\View;
use DateMalformedStringException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;

class AuthController
{
    /**
     * @throws Exception
     */
    public function index(): Response
    {

        $user = new FindUserQuery(new UserRepositories(), Session::get('user_id'));

        if (!empty($user->handle())) {
            return Response::make(Redirect::to('/profile'));
        }

        $view = new View('auth.login');

        return Response::make($view)->withHeaders([
            'Content-Type' => 'text/html',
        ])->withStatus(200);
    }

    public function login(): Response
    {
        $email = Request::input('email');
        $password = Request::input('password');

        $loginCommand = new LoginCommand(new UserRepositories(), $email, $password);

        if (!empty($loginCommand->execute())) {
            return Response::make(Redirect::to('/profile'));
        }

        return Response::make(Redirect::to('/login'));
    }

    /**
     * @throws DateMalformedStringException
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function register(): Response
    {
        $name = Request::input('name');
        $email = Request::input('email');
        $password = Request::input('password');

        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ];

        $rules = [
            'name' => 'required|min:3|max:150',
            'email' => 'required|min:4|max:150',
            'password' => 'required|min:4|max:255',
        ];

        $validator = new Validator($data, $rules);
        if ($validator->fails()) {
            return Response::make(Redirect::to('/register')
                ->with('data', $data)
                ->withErrors($validator->errors()));
        }

        $registerCommand = new RegisterCommand(new UserRepositories(), $name, $email, $password);
        $registerCommand->execute();

        $loginCommand = new LoginCommand(new UserRepositories(), $email, $password);

        if ($loginCommand->execute()) {
            return Response::make(Redirect::to('/profile'));
        }

        return Response::make(Redirect::to('/login'));
    }

    /**
     * @throws Exception
     */
    public function registerForm(): Response
    {
        $view = new View('auth.register', ['errors' => Session::error()]);

        return Response::make($view)->withHeaders([
            'Content-Type' => 'text/html',
        ])->withStatus(200);
    }

    public function logout(): Response
    {
        $logout = new LogoutCommand()->execute();

        if (!$logout) {
            throw new LogoutException('Logout failed');
        }

        return Response::make(Redirect::to('/login'));
    }
}
