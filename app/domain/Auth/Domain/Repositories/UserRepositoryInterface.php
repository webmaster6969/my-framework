<?php

declare(strict_types=1);

namespace App\domain\Auth\Domain\Repositories;

use App\domain\Auth\Domain\Model\Entities\User;

interface UserRepositoryInterface
{
    /**
     * @param string $name
     * @param string $email
     * @param string $password
     * @return User
     */
    public function create(string $name, string $email, string $password): User;

    /**
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function findByEmailAndPassword(string $email, string $password): ?User;

    /**
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     * @param User $user
     * @return void
     */
    public function update(User $user): void;

    /**
     * @param User $user
     * @return void
     */
    public function delete(User $user): void;

    /**
     * @param User $user
     * @param string $google2faSecret
     * @return bool
     */
    public function enableTwoFactor(User $user, string $google2faSecret): bool;

    /**
     * @param User $user
     * @return bool
     */
    public function disableTwoFactor(User $user): bool;
}