<?php

namespace Core\Support;

use Core\Support\Session\Session;
use Random\RandomException;

class Csrf
{
    /**
     * @throws RandomException
     */
    public static function token(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);
        return $token;
    }

    public static function check(string $token): bool
    {
        return Session::get('csrf_token') === $token;
    }

    /**
     * @throws RandomException
     */
    public static function get(): string
    {
        return self::token();
    }
}