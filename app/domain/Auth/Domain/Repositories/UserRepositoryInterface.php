<?php

namespace App\domain\Auth\Domain\Repositories;

use App\domain\Auth\Domain\Model\Entities\User;

interface UserRepositoryInterface
{
    public function create(string $name, string $email, string $password): User;
    public function findByEmailAndPassword(string $email, string $password): ?User;
    public function findById(int $id): ?User;
    public function update(User $user);
    public function delete(User $user);
}