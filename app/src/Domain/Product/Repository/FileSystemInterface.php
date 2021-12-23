<?php

declare(strict_types=1);

namespace Domain\Product\Repository;

interface FileSystemInterface
{
    public function getFileContent(string $filename): bool|string;
}
