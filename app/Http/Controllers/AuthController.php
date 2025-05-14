<?php

namespace App\Http\Controllers;

use Core\Database\DB;
use Core\Http\Request;
use Core\Support\Session\Session;
use Core\View\View;
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

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function login()
    {
        $email = Request::object()->input('email');
        $password = Request::object()->input('password');

        $user = DB::raw('SELECT * FROM users WHERE email = :email AND password = :password', ['email' => $email, 'password' => $password]);

        if (!empty($user)) {
            Session::set('auth', true);
            Session::set('name', 'John');
            header('Location: /hello');
            exit;
        }

        header('Location: /login');
        exit;
    }

    public function hello()
    {
        $session = Session::get('auth', false);

        if (empty($session)) {
            header('Location: /login');
            exit();
        }
        $view = new View();
        echo $view->render('auth.hello', ['name' => Session::get('name')]);
    }
}
