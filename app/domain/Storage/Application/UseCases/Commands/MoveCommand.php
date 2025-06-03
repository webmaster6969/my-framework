<?php

declare(strict_types=1);

namespace App\domain\Storage\Application\UseCases\Commands;

use App\domain\Common\Domain\CommandInterface;
use App\domain\Storage\Application\Repositories\StorageRepository;
use Core\Storage\File;

class MoveCommand implements CommandInterface
{
    /**
     * @param StorageRepository $storageRepository
     * @param File $file
     * @param string $path
     */
    public function __construct(
        private readonly StorageRepository $storageRepository,
        private readonly File              $file,
        private readonly string            $path
    )
    {
    }

    /**
     * @return bool
     */
    public function execute(): bool
    {
        return $this->storageRepository->move($this->file, $this->path);
    }
}