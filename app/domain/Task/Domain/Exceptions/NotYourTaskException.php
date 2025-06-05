<?php

declare(strict_types=1);

namespace App\domain\Task\Domain\Exceptions;

class NotYourTaskException extends \DomainException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Not your post')
    {
        parent::__construct($message);
    }
}