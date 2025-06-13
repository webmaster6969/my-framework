<?php

declare(strict_types=1);

namespace App\domain\Common\Domain\Exceptions;

class NotLoadFileTranslatorException extends \DomainException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Not load file translator')
    {
        parent::__construct($message);
    }
}