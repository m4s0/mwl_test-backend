<?php

declare(strict_types=1);

namespace Product\Repository;

interface FileSystemInterface
{
    public function getFileContent(string $filename);
}
