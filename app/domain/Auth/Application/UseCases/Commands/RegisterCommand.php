<?php

declare(strict_types=1);

namespace App\domain\Auth\Application\UseCases\Commands;

use App\domain\Auth\Application\Repositories\UserRepository;
use App\domain\Auth\Domain\Exceptions\RegisterException;
use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Common\Domain\CommandInterface;
use DateMalformedStringException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class RegisterCommand implements CommandInterface
{
    /**
     * @param UserRepository $userRepositories
     * @param string $name
     * @param string $email
     * @param string $password
     */
    public function __construct(
        private readonly UserRepository $userRepositories,
        private readonly string         $name,
        private readonly string         $email,
        private readonly string         $password,
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
        return $this->userRepositories->create($this->name, $this->email, $this->password);
    }
}