<?php

declare(strict_types=1);

namespace App\domain\Storage\Application\Repositories;

use App\domain\Storage\Domain\Repositories\StorageRepositoryInterface;
use Core\Storage\File;
use Core\Storage\Storage;
use Core\Support\App\App;

class StorageRepository implements StorageRepositoryInterface
{
    public function uplode(File $file): mixed
    {
        return $this->move($file, 'temporary');
    }

    public function move(File $file, string $path): mixed
    {
        $storage = new Storage(App::getBasePath() . '/storage/');
        return $storage->move($file, $path);
    }
}