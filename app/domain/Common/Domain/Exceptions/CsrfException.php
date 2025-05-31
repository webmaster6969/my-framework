<?php

declare(strict_types=1);

namespace App\domain\Common\Domain\Exceptions;

class CsrfException extends \DomainException
{
    public function __construct(string $message = 'Csrf error')
    {
        parent::__construct($message);
    }
}