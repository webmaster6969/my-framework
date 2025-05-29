<?php

declare(strict_types=1);

namespace Core\Storage;

use Core\Support\App\App;

class File implements IFile
{
    public function __construct(
        protected string $originalName,
        protected string $tmpPath,
        protected string $mimeType {
            get {
                return $this->mimeType;
            }
        },
        protected int    $size {
            get {
                return $this->size;
            }
        })
    {
    }

    public static function fromGlobals(string $key): ?self
    {
        if (!isset($_FILES[$key])) {
            return null;
        }

        $newPath = App::getBasePath() . '/storage/temporary/' . $_FILES[$key]['name'];
        move_uploaded_file($_FILES[$key]['tmp_name'], $newPath);

        return new self(
            $_FILES[$key]['name'],
            $newPath,
            $_FILES[$key]['type'],
            $_FILES[$key]['size']
        );
    }

    public function getClientOriginalName(): string
    {
        return $this->originalName;
    }

    public function move(string $destinationPath): bool
    {
        if (rename($this->tmpPath, $destinationPath)) {
            $this->tmpPath = $destinationPath; // обновляем путь
            return true;
        }
        return false;
    }

    public function getExtension(): string
    {
        return pathinfo($this->originalName, PATHINFO_EXTENSION);
    }

    public function getFilenameWithoutExtension(): string
    {
        return pathinfo($this->originalName, PATHINFO_FILENAME);
    }

    public function delete(): bool
    {
        return file_exists($this->tmpPath) && unlink($this->tmpPath);
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function path(): string
    {
        return $this->tmpPath;
    }
}