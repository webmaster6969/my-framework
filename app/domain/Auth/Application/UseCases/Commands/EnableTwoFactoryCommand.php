<?php

declare(strict_types=1);

namespace App\domain\Auth\Application\UseCases\Commands;

use App\domain\Auth\Application\Repositories\UserRepositorie;
use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Common\Domain\CommandInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class EnableTwoFactoryCommand implements CommandInterface
{
    public function __construct(
        private readonly UserRepositorie $userRepositories,
        private readonly User            $user,
        private readonly string          $secret,
    )
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function execute(): ?User
    {
        $this->user->setGoogle2faSecret($this->secret);
        $this->userRepositories->update($this->user);

        return $this->user;
    }
}