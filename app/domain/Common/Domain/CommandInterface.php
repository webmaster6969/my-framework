<?php

declare(strict_types=1);

namespace App\domain\Common\Domain;

interface CommandInterface
{
    /**
     * @return mixed
     */
    public function execute(): mixed;
}
