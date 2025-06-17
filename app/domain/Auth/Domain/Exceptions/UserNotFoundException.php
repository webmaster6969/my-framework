<?php

declare(strict_types=1);

namespace App\domain\Auth\Domain\Exceptions;

class UserNotFoundException extends \DomainException
{
    public function __construct(string $message = 'User not found')
    {
        parent::__construct($message);
    }
}