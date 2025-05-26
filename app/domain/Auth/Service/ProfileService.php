<?php

namespace App\domain\Auth\Service;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Domain\Model\Entities\User;
use Core\Support\Session\Session;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class ProfileService
{
    public function __construct(private readonly UserRepositories $userRepositories) {}

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update(User $user): bool
    {
        $this->userRepositories->update($user);
        return true;
    }
}