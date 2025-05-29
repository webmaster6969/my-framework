<?php

declare(strict_types=1);

namespace App\domain\Auth\Application\UseCases\Commands;

use App\domain\Auth\Domain\Exceptions\LogoutException;
use App\domain\Common\Domain\CommandInterface;
use Core\Support\Session\Session;

class LogoutCommand implements CommandInterface
{
    public function __construct()
    {
    }

    public function execute(): bool
    {
        try {
            Session::destroy();
        } catch (\Exception $e) {
            throw new LogoutException($e->getMessage());
        }

        return true;
    }
}