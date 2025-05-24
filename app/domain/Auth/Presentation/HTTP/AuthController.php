<?php

namespace App\domain\Auth\Presentation\HTTP;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Service\AuthService;
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
        $session = Session::get('auth', false);

        if (!empty($session)) {
            header('Location: /profile');
            exit;
        }

        $view = new View();
        echo $view->render('auth.login');
    }

    /**
     * @throws Exception
     */
    public function profile()
    {
        $authService = new AuthService(new UserRepositories());
        $user = $authService->getUser();

        if (empty($user)) {
            header('Location: /login');
            exit;
        }

        $view = new View();
        echo $view->render('auth.profile', ['user' => $user]);
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

        $authService = new AuthService(new UserRepositories());
        $user = $authService->login($email, $password);

        if ($user) {
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

        $authService = new AuthService(new UserRepositories());
        if ($user = $authService->register($name, $email, $password)) {
            $authService->login($user->getEmail(), $user->getPassword());
            header('Location: /profile');
            exit;
        }

        header('Location: /register');
        exit;
    }

    public function registerForm()
    {
        $view = new View();
        echo $view->render('auth.register');
    }

    public function logout()
    {
        $authService = new AuthService(new UserRepositories());
        $authService->logout();
        header('Location: /login');
        exit;
    }
}
