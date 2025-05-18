<?php

namespace App\Http\Controllers;

use Core\Support\Auth\Auth;
use Core\View\View;
use Exception;

class HomeController
{
    /**
     * @throws Exception
     */
    public function index()
    {
        $user = Auth::user();

        if (empty($user)) {
            header('Location: /login');
            exit();
        }
        $view = new View();
        echo $view->render('auth.hello', ['name' => $user->getName()]);
    }

    public function hello()
    {
        $user = Auth::user();

        if (empty($user)) {
            header('Location: /login');
            exit();
        }
        $view = new View();
        echo $view->render('auth.hello', ['name' => $user->getName()]);
    }
}
