<?php

declare(strict_types=1);

namespace Core\Storage;

interface IFile
{
    public static function fromGlobals(string $key): ?self;
    public function getClientOriginalName(): string;
    public function move(string $destinationPath): bool;
    public function getExtension(): string;
    public function getFilenameWithoutExtension(): string;
    public function delete(): bool;
    public function getMimeType(): string;
    public function getSize(): int;
    public function path(): string;
}