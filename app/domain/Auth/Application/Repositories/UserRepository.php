<?php

declare(strict_types=1);

namespace App\domain\Auth\Application\Repositories;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Auth\Domain\Repositories\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $password
     * @return User
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
        $user = $this
            ->em
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

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
        return $this
            ->em
            ->getRepository(User::class)
            ->find($id);
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this
            ->em
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);
    }

    /**
     * @param User $user
     * @return void
     */
    public function update(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param User $user
     * @return void
     */
    public function delete(User $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * @param User $user
     * @param string $google2faSecret
     * @return bool
     */
    public function enableTwoFactor(User $user, string $google2faSecret): bool
    {
        $user->setGoogle2faSecret($google2faSecret);
        $this->em->persist($user);
        $this->em->flush();

        return true;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function disableTwoFactor(User $user): bool
    {
        $user->setGoogle2faSecret(null);
        $this->em->persist($user);
        $this->em->flush();

        return true;
    }
}