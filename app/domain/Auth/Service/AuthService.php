<?php

namespace App\domain\Auth\Service;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Domain\Model\Entities\User;
use Core\Support\Session\Session;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class AuthService
{
    public function __construct(private readonly UserRepositories $userRepositories) {}

    public function login(string $email, string $password): ?User
    {
        $user = $this->userRepositories->findByEmailAndPassword($email, $password);

        if (empty($user)) {
            return null;
        }

        Session::set('user_id', $user->getId());
        return $user;
    }

    public function logout(): void
    {
        Session::destroy();
    }

    /**
     * @throws OptimisticLockException
     * @throws \DateMalformedStringException
     * @throws ORMException
     */
    public function register(string $name, string $email, string $password): User
    {
        return $this->userRepositories->create($name, $email, $password);
    }

    public function getUser(): ?User
    {
        $userId = Session::get('user_id');

        if (empty($userId)) {
            return null;
        }

        return $this->userRepositories->findById(Session::get('user_id'));
    }
}