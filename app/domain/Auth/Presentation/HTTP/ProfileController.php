<?php

namespace App\domain\Auth\Presentation\HTTP;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Service\AuthService;
use App\domain\Auth\Service\ProfileService;
use Core\Http\Request;
use Core\Support\Csrf\Csrf;
use Core\View\View;
use Exception;

class ProfileController
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
            exit;
        }

        $view = new View();
        echo $view->render('auth.profile', ['user' => $user]);
    }

    public function update()
    {
        $name = Request::input('name');
        $csrfToken = Request::input('csrf_token');

        if (!Csrf::check($csrfToken)) {
            header('Location: /login');
            exit;
        }

        $authService = new AuthService(new UserRepositories());
        $user = $authService->getUser();
        $user->setName($name);
        $authService = new ProfileService(new UserRepositories());
        $authService->update($user);

        header('Location: /profile');
        exit;
    }
}
