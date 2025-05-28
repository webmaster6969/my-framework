<?php

namespace App\domain\Auth\Domain\Exceptions;

class RegisterException extends \DomainException
{
    public function __construct(string $message = 'Logout error')
    {
        parent::__construct($message);
    }
}