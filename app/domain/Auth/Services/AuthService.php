<?php

namespace App\domain\Auth\Services;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Application\UseCases\Queries\FindUserQuery;
use App\domain\Auth\Domain\Model\Entities\User;
use Core\Support\Session\Session;

class AuthService
{
    public static function getUser(): ?User
    {
        $userId = Session::get('user_id');
        $userId = is_int($userId) ? $userId : null;

        $findUserQuery = new FindUserQuery(new UserRepositories(), $userId);
        return $findUserQuery->handle();
    }
}