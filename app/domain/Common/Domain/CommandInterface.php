<?php

namespace App\domain\Common\Domain;

interface CommandInterface
{
    public function execute(): mixed;
}
