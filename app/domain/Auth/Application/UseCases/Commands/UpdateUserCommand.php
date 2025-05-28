<?php

namespace App\domain\Auth\Application\UseCases\Commands;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Common\Domain\CommandInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class UpdateUserCommand implements CommandInterface
{
    public function __construct(
        private readonly UserRepositories $userRepositories,
        private readonly User             $user,
    )
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function execute(): ?User
    {
        $this->userRepositories->update($this->user);

        return $this->user;
    }
}