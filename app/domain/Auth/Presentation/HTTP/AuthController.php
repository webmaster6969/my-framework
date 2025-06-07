<?php

declare(strict_types=1);

namespace App\domain\Auth\Presentation\HTTP;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Application\UseCases\Commands\LoginCommand;
use App\domain\Auth\Application\UseCases\Commands\LogoutCommand;
use App\domain\Auth\Application\UseCases\Commands\RegisterCommand;
use App\domain\Auth\Application\UseCases\Queries\FindUserByEmailQuery;
use App\domain\Auth\Domain\Exceptions\LogoutException;
use App\domain\Auth\Services\AuthService;
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
        $user = AuthService::getUser();

        if (!empty($user)) {
            return Response::make(Redirect::to('/profile'));
        }

        $data = Session::flash('data');
        $data = is_array($data) ? $data : [];
        $defaults = ['email' => '',];
        $data = array_merge($defaults, $data);

        $view = new View('auth.login', [
                'data' => $data,
                'errors' => Session::error()
            ]
        );

        return Response::make($view)->withHeaders([
            'Content-Type' => 'text/html',
        ])->withStatus(200);
    }

    /**
     * @return Response
     */
    public function login(): Response
    {
        $data = Request::only(['email', 'password']);

        $email = is_string($data['email']) ? $data['email'] : '';
        $password = is_string($data['password']) ? $data['password'] : '';

        $validator = new Validator($data, [
            'email' => 'required|min:4|max:150',
            'password' => 'required|min:4|max:255',
        ]);

        if ($validator->fails()) {
            return Response::make(
                Redirect::to('/login')
                ->with('data', $data)
                ->withErrors($validator->errors()));
        }

        $loginCommand = new LoginCommand(new UserRepositories(), $email, $password);

        if (!empty($loginCommand->execute())) {
            return Response::make(Redirect::to('/profile'));
        }

        return Response::make(
            Redirect::to('/login')
                ->with('data', $data)
                ->withErrors(
                    [
                        'email' => ['Введенные данные неверны'],
                        'password' => ['Введенные данные неверны'],
                    ]
                )
        );
    }

    /**
     * @throws DateMalformedStringException
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function register(): Response
    {
        $data = Request::only(['name', 'email', 'password']);

        $rules = [
            'name' => 'required|min:3|max:150',
            'email' => 'required|min:4|max:150',
            'password' => 'required|min:4|max:255',
        ];

        $validator = new Validator($data, $rules);
        if ($validator->fails()) {
            return Response::make(
                Redirect::to('/register')
                ->with('data', $data)
                ->withErrors($validator->errors()));
        }

        $name = is_string($data['name']) ? $data['name'] : '';
        $email = is_string($data['email']) ? $data['email'] : '';
        $password = is_string($data['password']) ? $data['password'] : '';

        $findUserQuery = new FindUserByEmailQuery(new UserRepositories(), $email);
        if (!empty($findUserQuery->handle())) {
            return Response::make(
                Redirect::to('/register')
                ->with('data', $data)
                ->withErrors(['email' => ['Пользователь с таким email уже существует']]));
        }

        $registerCommand = new RegisterCommand(new UserRepositories(), $name, $email, $password);
        $registerCommand->execute();

        $loginCommand = new LoginCommand(new UserRepositories(), $email, $password);

        if ($loginCommand->execute()) {
            return Response::make(Redirect::to('/profile'));
        }

        return Response::make(
            Redirect::to('/login')
                ->with('data', $data)
                ->withErrors(
                    [
                        'email' => ['Введенные данные неверны'],
                        'password' => ['Введенные данные неверны'],
                    ]
                )
        );
    }

    /**
     * @throws Exception
     */
    public function registerForm(): Response
    {
        $data = Session::flash('data');
        $data = is_array($data) ? $data : [];
        $defaults = ['title' => '', 'description' => '', 'start_task' => '', 'end_task' => ''];
        $data = array_merge($defaults, $data);

        $view = new View('auth.register', ['data' => $data, 'errors' => Session::error()]);

        return Response::make($view)->withHeaders([
            'Content-Type' => 'text/html',
        ])->withStatus(200);
    }

    /**
     * @return Response
     */
    public function logout(): Response
    {
        $logout = new LogoutCommand()->execute();

        if (!$logout) {
            throw new LogoutException('Logout failed');
        }

        return Response::make(Redirect::to('/login'));
    }
}