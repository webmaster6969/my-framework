<?php

declare(strict_types=1);

namespace App\domain\Storage\Domain\Repositories;

use Core\Storage\File;

interface StorageRepositoryInterface
{
    public function uplode(File $file): bool;

    public function move(File $file, string $path): bool;
}