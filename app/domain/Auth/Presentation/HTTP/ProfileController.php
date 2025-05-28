<?php

namespace App\domain\Auth\Presentation\HTTP;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Application\UseCases\Commands\UpdateUserCommand;
use App\domain\Auth\Application\UseCases\Queries\FindUserQuery;
use Core\Http\Request;
use Core\Support\Csrf\Csrf;
use Core\Support\Session\Session;
use Core\View\View;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;

class ProfileController
{
    /**
     * @throws Exception
     */
    public function index()
    {
        $user = new FindUserQuery(new UserRepositories(), Session::get('user_id'))->handle();

        if (empty($user)) {
            header('Location: /login');
            exit;
        }

        $view = new View();
        echo $view->render('auth.profile', ['user' => $user]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update()
    {
        $name = Request::input('name');
        $csrfToken = Request::input('csrf_token');

        if (!Csrf::check($csrfToken)) {
            header('Location: /login');
            exit;
        }

        $findUserQuery = new FindUserQuery(new UserRepositories(), Session::get('user_id'));
        $user = $findUserQuery->handle();
        $user->setName($name);
        $updateUserCommand = new UpdateUserCommand(new UserRepositories(), $user);
        $user = $updateUserCommand->execute();

        header('Location: /profile');
        exit;
    }
}
