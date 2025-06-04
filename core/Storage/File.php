<?php

declare(strict_types=1);

namespace Core\Storage;

use Core\Support\App\App;

class File implements IFile
{
    /**
     * @param string $originalName
     * @param string $tmpPath
     * @param string $mimeType
     * @param int $size
     */
    public function __construct(
        protected string $originalName,
        protected string $tmpPath,
        protected string $mimeType,
        protected int    $size,
    )
    {
    }

    /**
     * @param string $key
     * @return self|null
     */
    public static function fromGlobals(string $key): ?self
    {
        if (!isset($_FILES[$key]) || !is_array($_FILES[$key])) {
            return null;
        }

        $file = $_FILES[$key];

        // Валидация ключей и типов
        if (
            !isset($file['name'], $file['tmp_name'], $file['type'], $file['size']) ||
            !is_string($file['name']) ||
            !is_string($file['tmp_name']) ||
            !is_string($file['type']) ||
            !is_int($file['size']) // Важно: иногда приходит string, проверка может быть строже
        ) {
            return null;
        }

        $newPath = App::getBasePath() . '/storage/temporary/' . $file['name'];
        if (!move_uploaded_file($file['tmp_name'], $newPath)) {
            return null;
        }

        return new self(
            $file['name'],
            $newPath,
            $file['type'],
            $file['size']
        );
    }

    /**
     * @param string $name Имя поля формы (например, 'images')
     * @return File[]|null
     */
    public static function fromMultipleGlobals(string $name): ?array
    {
        if (
            !isset($_FILES[$name]) ||
            !is_array($_FILES[$name]) ||
            !isset($_FILES[$name]['name']) ||
            !is_array($_FILES[$name]['name'])
        ) {
            return null;
        }

        /** @var array{name: string[], tmp_name: string[], type: string[], size: int[], error: int[]} $files */
        $files = $_FILES[$name];
        $count = count($files['name']);
        $result = [];

        for ($i = 0; $i < $count; $i++) {
            $originalName = $files['name'][$i] ?? null;
            $tmpName      = $files['tmp_name'][$i] ?? null;
            $mimeType     = $files['type'][$i] ?? null;
            $size         = $files['size'][$i] ?? null;
            $error        = $files['error'][$i] ?? null;

            // Проверка типов
            if (
                !is_string($originalName) ||
                !is_string($tmpName) ||
                !is_string($mimeType) ||
                !is_int($size) ||
                !is_int($error)
            ) {
                continue;
            }

            if (
                $error !== UPLOAD_ERR_OK ||
                !is_uploaded_file($tmpName)
            ) {
                continue;
            }

            $newPath = App::getBasePath() . '/storage/temporary/' . basename($originalName);
            if (!move_uploaded_file($tmpName, $newPath)) {
                continue;
            }

            $result[] = new self($originalName, $newPath, $mimeType, $size);
        }

        return $result ?: null;
    }

    /**
     * @return string
     */
    public function getClientOriginalName(): string
    {
        return $this->originalName;
    }

    /**
     * @param string $destinationPath
     * @return bool
     */
    public function move(string $destinationPath): bool
    {
        if (rename($this->tmpPath, $destinationPath)) {
            $this->tmpPath = $destinationPath; // обновляем путь
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return pathinfo($this->originalName, PATHINFO_EXTENSION);
    }

    /**
     * @return string
     */
    public function getFilenameWithoutExtension(): string
    {
        return pathinfo($this->originalName, PATHINFO_FILENAME);
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return file_exists($this->tmpPath) && unlink($this->tmpPath);
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return $this->tmpPath;
    }
}