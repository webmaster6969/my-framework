<?php

namespace App\Http\Controllers;

use Core\Http\Request;
use Core\Support\Auth\Auth;
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
            header('Location: /hello');
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

        if (Auth::auth($email, $password)) {
            header('Location: /hello');
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

        $password = password_hash($password, PASSWORD_BCRYPT);
        if (Auth::register($name, $email, $password)) {
            header('Location: /hello');
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
        Auth::logout();
        header('Location: /login');
        exit;
    }
}
