<?php

namespace App\domain\Storage\Domain\Repositories;

use Core\Storage\File;

interface StorageRepositoryInterface
{
    public function uplode(File $file): mixed;

    public function move(File $file, string $path): mixed;
}