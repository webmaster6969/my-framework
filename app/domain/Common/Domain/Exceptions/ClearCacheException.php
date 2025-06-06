<?php

declare(strict_types=1);

namespace App\domain\Common\Domain\Exceptions;

class ClearCacheException extends \DomainException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Cache not cleared')
    {
        parent::__construct($message);
    }
}