<?php

namespace App\domain\Auth\Services;

use App\domain\Auth\Application\Repositories\UserRepository;
use App\domain\Auth\Application\UseCases\Queries\FindUserQuery;
use App\domain\Auth\Domain\Model\Entities\User;
use Core\Database\DB;
use Core\Support\Session\Session;

class AuthService
{
    /**
     * @return User|null
     */
    public static function getUser(): ?User
    {
        $userId = Session::get('user_id');
        $userId = is_int($userId) ? $userId : null;

        $findUserQuery = new FindUserQuery(new UserRepository(DB::getEntityManager()), $userId);
        return $findUserQuery->handle();
    }
}