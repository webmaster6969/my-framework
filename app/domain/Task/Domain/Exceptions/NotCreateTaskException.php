<?php

declare(strict_types=1);

namespace App\domain\Task\Domain\Exceptions;

class NotCreateTaskException extends \DomainException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Not create task')
    {
        parent::__construct($message);
    }
}