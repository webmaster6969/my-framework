<?php

namespace App\domain\Auth\Application\UseCases\Commands;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Common\Domain\CommandInterface;
use Core\Support\Session\Session;

class LoginCommand implements CommandInterface
{

    public function __construct(
        private readonly UserRepositories $userRepositories,
        private readonly string           $email,
        private readonly string           $password,
    )
    {
    }

    public function execute(): mixed
    {
        $user = $this->userRepositories->findByEmailAndPassword($this->email, $this->password);

        if (empty($user)) {
            return null;
        }

        Session::set('user_id', $user->getId());
        return $user;
    }
}