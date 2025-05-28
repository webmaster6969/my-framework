<?php

namespace App\domain\Common\Domain;

interface QueryInterface
{
    public function handle(): mixed;
}
