<?php

namespace App\Http\Controllers;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Service\AuthService;
use Core\View\View;
use Exception;

class HomeController
{
    /**
     * @throws Exception
     */
    public function index()
    {
        $authService = new AuthService(new UserRepositories());
        $user = $authService->getUser();

        if (empty($user)) {
            header('Location: /login');
            exit();
        }

        $view = new View();
        echo $view->render('auth.hello', ['name' => $user->getName()]);
    }

    public function hello()
    {
        $authService = new AuthService(new UserRepositories());
        $user = $authService->getUser();

        if (empty($user)) {
            header('Location: /login');
            exit();
        }
        $view = new View();
        echo $view->render('auth.hello', ['name' => $user->getName()]);
    }
}
