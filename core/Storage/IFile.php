<?php

declare(strict_types=1);

namespace Core\Storage;

interface IFile
{
    /**
     * @param string $key
     * @return self|null
     */
    public static function fromGlobals(string $key): ?self;

    /**
     * @return string
     */
    public function getClientOriginalName(): string;

    /**
     * @param string $destinationPath
     * @return bool
     */
    public function move(string $destinationPath): bool;

    /**
     * @return string
     */
    public function getExtension(): string;

    /**
     * @return string
     */
    public function getFilenameWithoutExtension(): string;

    /**
     * @return bool
     */
    public function delete(): bool;

    /**
     * @return string
     */
    public function getMimeType(): string;

    /**
     * @return int
     */
    public function getSize(): int;

    /**
     * @return string
     */
    public function path(): string;
}