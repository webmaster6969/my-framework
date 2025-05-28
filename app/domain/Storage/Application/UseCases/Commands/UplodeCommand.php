<?php

namespace App\domain\Storage\Application\UseCases\Commands;

use App\domain\Common\Domain\CommandInterface;
use App\domain\Storage\Application\Repositories\StorageRepository;
use Core\Storage\File;

class UplodeCommand implements CommandInterface
{
    public function __construct(
        private readonly StorageRepository $storageRepository,
        private readonly File              $file,
    )
    {
    }

    public function execute(): bool
    {
        return $this->storageRepository->uplode($this->file);
    }
}