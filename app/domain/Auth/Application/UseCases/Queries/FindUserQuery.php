<?php

declare(strict_types=1);

namespace App\domain\Auth\Application\UseCases\Queries;

use App\domain\Auth\Application\Repositories\UserRepositories;
use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Common\Domain\QueryInterface;

class FindUserQuery implements QueryInterface
{
    public function __construct(
        private readonly UserRepositories $userRepositories,
        private readonly ?int             $userIid
    )
    {
    }

    public function handle(): ?User
    {
        if (empty($this->userIid)) {
            return null;
        }

        return $this->userRepositories->findById($this->userIid);
    }
}
