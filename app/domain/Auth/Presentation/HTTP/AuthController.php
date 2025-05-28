<?php

namespace App\domain\Auth\Presentation\HTTP;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Application\UseCases\Commands\LoginCommand;
use App\domain\Auth\Application\UseCases\Commands\LogoutCommand;
use App\domain\Auth\Application\UseCases\Commands\RegisterCommand;
use App\domain\Auth\Application\UseCases\Queries\FindUserQuery;
use Core\Http\Request;
use Core\Support\Csrf\Csrf;
use Core\Support\Session\Session;
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
    public function index()
    {

        $user = new FindUserQuery(new UserRepositories(), Session::get('user_id'));

        if (!empty($user->handle())) {
            header('Location: /profile');
            exit;
        }

        $view = new View();
        echo $view->render('auth.login');
    }

    public function login()
    {
        $email = Request::input('email');
        $password = Request::input('password');
        $csrfToken = Request::input('csrf_token');

        if (!Csrf::check($csrfToken)) {
            header('Location: /login');
            exit;
        }

        $loginCommand = new LoginCommand(new UserRepositories(), $email, $password);

        if (!empty($loginCommand->execute())) {
            header('Location: /profile');
            exit;
        }

        header('Location: /login');
        exit;
    }

    /**
     * @throws DateMalformedStringException
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function register(): bool
    {
        $name = Request::input('name');
        $email = Request::input('email');
        $password = Request::input('password');
        $csrfToken = Request::input('csrf_token');

        if (!Csrf::check($csrfToken)) {
            header('Location: /register');
            exit;
        }

        $registerCommand = new RegisterCommand(new UserRepositories(), $name, $email, $password);
        $registerCommand->execute();

        $loginCommand = new LoginCommand(new UserRepositories(), $email, $password);

        if ($loginCommand->execute()) {
            header('Location: /profile');
            exit;
        }

        header('Location: /login');
        exit;
    }

    public function registerForm()
    {
        $view = new View();
        echo $view->render('auth.register');
    }

    public function logout()
    {
        $logout = new LogoutCommand()->execute();

        if ($logout) {
            header('Location: /login');
            exit;
        }
    }
}
