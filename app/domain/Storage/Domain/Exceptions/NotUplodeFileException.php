<?php

declare(strict_types=1);

namespace App\domain\Storage\Domain\Exceptions;

class NotUplodeFileException extends \DomainException
{
    public function __construct(string $message = 'Not upload file')
    {
        parent::__construct($message);
    }
}