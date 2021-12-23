<?php

declare(strict_types=1);

namespace Domain\Product\Repository;

class FileSystem implements FileSystemInterface
{
    public function getFileContent(string $filename): bool|string
    {
        return file_get_contents($filename);
    }
}
