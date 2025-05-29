<?php

declare(strict_types=1);

namespace App\domain\Auth\Application\UseCases\Commands;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Domain\Exceptions\RegisterException;
use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Common\Domain\CommandInterface;
use DateMalformedStringException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class RegisterCommand implements CommandInterface
{
    public function __construct(
        private readonly UserRepositories $userRepositories,
        private readonly string           $name,
        private readonly string           $email,
        private readonly string           $password,
    )
    {
    }

    /**
     * @throws DateMalformedStringException
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function execute(): ?User
    {
        $user = $this->userRepositories->create($this->name, $this->email, $this->password);

        if (empty($user)) {
            throw new RegisterException('User not created');
        }

        return $user;
    }
}