<?php

declare(strict_types=1);

namespace App\domain\Auth\Application\Repositories;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Auth\Domain\Repositories\UserRepositoryInterface;
use Core\Database\DB;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use RuntimeException;

class UserRepositories implements UserRepositoryInterface
{
    /**
     * @var EntityManager
     */
    private EntityManagerInterface $em;

    public function __construct()
    {
        $em = DB::getEntityManager();
        if (!$em) {
            throw new RuntimeException('EntityManager not initialized.');
        }

        $this->em = $em;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException|\DateMalformedStringException
     */
    public function create(string $name, string $email, string $password): User
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $user = new User($name, $email, $hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function findByEmailAndPassword(string $email, string $password): ?User
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user && password_verify($password, $user->getPassword())) {
            return $user;
        }

        return null;
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return $this->em->getRepository(User::class)->find($id);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function delete(User $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function enableTwoFactor(User $user, string $google2faSecret): bool
    {
        $user->setGoogle2faSecret($google2faSecret);
        $this->em->persist($user);
        $this->em->flush();

        return true;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function disableTwoFactor(User $user): bool
    {
        $user->setGoogle2faSecret(null);
        $this->em->persist($user);
        $this->em->flush();

        return true;
    }
}