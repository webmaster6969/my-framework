<?php

declare(strict_types=1);

namespace App\domain\Storage\Domain\Repositories;

use Core\Storage\File;

interface StorageRepositoryInterface
{
    /**
     * @param File $file
     * @return bool
     */
    public function upload(File $file): bool;

    /**
     * @param File $file
     * @param string $path
     * @return bool
     */
    public function move(File $file, string $path): bool;
}