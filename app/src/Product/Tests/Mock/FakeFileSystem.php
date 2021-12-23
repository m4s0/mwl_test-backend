<?php

declare(strict_types=1);

namespace Product\Tests\Mock;

use Product\Repository\FileSystemInterface;

class FakeFileSystem implements FileSystemInterface
{
    protected string $fileContent = '{ "items": [] }';

    public function getFileContent(string $filename): bool|string
    {
        return file_get_contents($filename);
    }

    public function setFileContent(string $filename, string $value): void
    {
        file_put_contents($filename, $value);
    }
}
