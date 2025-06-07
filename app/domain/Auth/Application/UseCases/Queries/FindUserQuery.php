<?php

declare(strict_types=1);

namespace App\domain\Auth\Application\UseCases\Queries;

use App\domain\Auth\Application\Repositories\UserRepositorie;
use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Common\Domain\QueryInterface;

class FindUserQuery implements QueryInterface
{
    /**
     * @param UserRepositorie $userRepositories
     * @param int|null $userIid
     */
    public function __construct(
        private readonly UserRepositorie $userRepositories,
        private readonly ?int            $userIid
    )
    {
    }

    /**
     * @return User|null
     */
    public function handle(): ?User
    {
        if (empty($this->userIid)) {
            return null;
        }

        return $this->userRepositories->findById($this->userIid);
    }
}
