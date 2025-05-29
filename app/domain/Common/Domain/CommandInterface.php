<?php

declare(strict_types=1);

namespace App\domain\Common\Domain;

interface CommandInterface
{
    public function execute(): mixed;
}
