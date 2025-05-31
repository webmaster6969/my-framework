<?php

declare(strict_types=1);

namespace App\domain\Auth\Presentation\HTTP;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Application\UseCases\Commands\LoginCommand;
use App\domain\Auth\Application\UseCases\Commands\LogoutCommand;
use App\domain\Auth\Application\UseCases\Commands\RegisterCommand;
use App\domain\Auth\Application\UseCases\Queries\FindUserQuery;
use App\domain\Common\Domain\Exceptions\CsrfException;
use Core\Http\Request;
use Core\Routing\Redirect;
use Core\Support\Csrf\Csrf;
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
    public function index()
    {

        $user = new FindUserQuery(new UserRepositories(), Session::get('user_id'));

        if (!empty($user->handle())) {
            Redirect::to('/profile')->send();
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
            throw new CsrfException('Csrf error');
        }

        $loginCommand = new LoginCommand(new UserRepositories(), $email, $password);

        if (!empty($loginCommand->execute())) {
            Redirect::to('/profile')->send();
        }

        Redirect::to('/login')->send();
    }

    /**
     * @throws DateMalformedStringException
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function register()
    {
        $name = Request::input('name');
        $email = Request::input('email');
        $password = Request::input('password');
        $csrfToken = Request::input('csrf_token');

        if (!Csrf::check($csrfToken)) {
            throw new CsrfException('Csrf error');
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ];

        $rules = [
            'name' => 'required|min:3|max:150',
            'email' => 'required|min:4|max:150',
            'password' => 'required|min:4|max:255',
        ];

        $validator = new Validator($data, $rules);
        if ($validator->fails()) {
            Redirect::to('/register')
                ->with('data', $data)
                ->withErrors($validator->errors())
                ->send();
        }

        $registerCommand = new RegisterCommand(new UserRepositories(), $name, $email, $password);
        $registerCommand->execute();

        $loginCommand = new LoginCommand(new UserRepositories(), $email, $password);

        if ($loginCommand->execute()) {
            Redirect::to('/profile')->send();
        }

        Redirect::to('/login')->send();
    }

    public function registerForm()
    {
        $view = new View();
        echo $view->render('auth.register', ['errors' => Session::error()]);
    }

    public function logout()
    {
        $logout = new LogoutCommand()->execute();

        if ($logout) {
            Redirect::to('/login')->send();
        }
    }
}
