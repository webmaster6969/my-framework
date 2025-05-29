<?php

declare(strict_types=1);

namespace App\domain\Common\Domain;

interface QueryInterface
{
    public function handle(): mixed;
}
