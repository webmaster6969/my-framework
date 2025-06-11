<?php

declare(strict_types=1);

namespace App\domain\Common\Domain\Exceptions;

class EncryptionKeyIsNotFindException extends \DomainException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Encryption key is not find')
    {
        parent::__construct($message);
    }
}