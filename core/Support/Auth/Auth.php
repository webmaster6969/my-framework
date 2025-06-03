<?php

declare(strict_types=1);

namespace Core\Support\Auth;

use App\domain\Auth\Domain\Model\Entities\User;
use Core\Database\DB;
use Core\Support\Session\Session;
use DateMalformedStringException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class Auth
{
    /**
     * @throws DateMalformedStringException
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public static function register(string $name, string $email, string $password): bool
    {
        $em = DB::getEntityManager();
        if ($em === null) {
            throw new \RuntimeException('EntityManager is not available.');
        }

        $user = new User($name, $email, $password);
        $em->persist($user);
        $em->flush();
        Session::set('user_id', $user->getId());

        return true;
    }

    /**
     * @return User|null
     */
    public static function user(): ?User
    {
        $em = DB::getEntityManager();
        if ($em === null) {
            throw new \RuntimeException('EntityManager is not available.');
        }

        if (empty(Session::get('user_id'))) {
            return null;
        }

        return $em->getRepository(User::class)
            ->find(Session::get('user_id'));
    }

    /**
     * @return bool
     */
    public static function check(): bool
    {
        return !empty(Session::get('user_id'));
    }

    /**
     * @return void
     */
    public static function logout(): void
    {
        Session::forget('user_id');
    }

    /**
     * @param string $email
     * @param string $password
     * @return bool
     */
    public static function auth(string $email, string $password): bool
    {
        $em = DB::getEntityManager();
        if ($em === null) {
            throw new \RuntimeException('EntityManager is not available.');
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user !== null && password_verify($password, $user->getPassword())) {
            Session::set('user_id', $user->getId());
            return true;
        }

        return false;
    }
}