<?php

declare(strict_types=1);

namespace App\domain\Common\Domain\Exceptions;

class ValidationNoFindMethodException extends \DomainException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Validation no find method')
    {
        parent::__construct($message);
    }
}