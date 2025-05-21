<?php

namespace Core\Support\Auth;

use App\domain\Auth\Domain\Model\Entities\User;
use Core\Database\DB;
use Core\Support\Session\Session;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class Auth
{

    /**
     * @throws \DateMalformedStringException
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public static function register(string $name, string $email, string $password): bool
    {
        $user = new User($name, $email, $password, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
        DB::getEntityManager()->persist($user);
        DB::getEntityManager()->flush();
        Session::set('user_id', $user->getId());

        return true;
    }

    public static function user(): ?User
    {
        if (empty(Session::get('user_id'))) {
            return null;
        }

        return DB::getEntityManager()
            ->getRepository(User::class)
            ->find(Session::get('user_id'));
    }

    public static function check(): bool
    {
        return !empty(Session::get('user_id'));
    }

    public static function logout(): void
    {
        Session::forget('user_id');
    }

    public static function auth(string $email, string $password): bool
    {
        $user = DB::getEntityManager()
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (password_verify($password, $user->getPassword())) {
            Session::set('user_id', $user->getId());
            return true;
        }

        return false;
    }
}