<?php

namespace App\Http\Controllers;

use Core\Database\DB;
use Core\Http\Request;
use Core\Support\Session\Session;
use Core\View\View;
use Database\Entities\User;
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
     */
    public function login()
    {
        $email = Request::input('email');
        $password = Request::input('password');

        $user = DB::getEntityManager()
            ->getRepository(User::class)
            ->findOneBy(['email' => $email, 'password' => $password]);

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
