<?php

declare(strict_types=1);

namespace App\domain\Auth\Domain\Exceptions;

class LogoutException extends \DomainException
{
    public function __construct(string $message = 'Logout error')
    {
        parent::__construct($message);
    }
}