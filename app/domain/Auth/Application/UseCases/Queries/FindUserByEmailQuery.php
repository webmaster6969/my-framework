<?php

declare(strict_types=1);

namespace App\domain\Auth\Application\UseCases\Queries;

use App\domain\Auth\Application\Repositories\UserRepository;
use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Common\Domain\QueryInterface;

class FindUserByEmailQuery implements QueryInterface
{
    /**
     * @param UserRepository $userRepositories
     * @param string $email
     */
    public function __construct(
        private readonly UserRepository $userRepositories,
        private readonly string         $email
    )
    {
    }

    /**
     * @return User|null
     */
    public function handle(): ?User
    {
        return $this->userRepositories->findByEmail($this->email);
    }
}
