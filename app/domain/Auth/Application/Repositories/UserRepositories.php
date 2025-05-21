<?php

namespace App\domain\Auth\Application\Repositories;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Auth\Domain\Repositories\UserRepositoryInterface;
use Core\Database\DB;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class UserRepositories implements UserRepositoryInterface
{

    /**
     * @throws \DateMalformedStringException
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function create(string $name, string $email, string $password): User
    {
        $user = new User($name, $email, password_hash($password, PASSWORD_BCRYPT), date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
        DB::getEntityManager()->persist($user);
        DB::getEntityManager()->flush();
        return $user;
    }

    public function findByEmailAndPassword(string $email, string $password): ?User
    {
        $user = DB::getEntityManager()
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (empty($user)) {
            return null;
        }

        if (password_verify($password, $user->getPassword())) {
            return $user;
        }

        return null;
    }

    public function findById(int $id): ?User
    {
        return DB::getEntityManager()
            ->getRepository(User::class)
            ->find($id);
    }

    public function update(User $user)
    {
        // TODO: Implement update() method.
    }

    public function delete(User $user)
    {
        // TODO: Implement delete() method.
    }
}