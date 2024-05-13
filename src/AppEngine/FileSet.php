<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

class FileSet
{
    /** @var array<int,array<int,string>> */
    private array $fileList = [];

    /** @see https://www.php.net/manual/pt_BR/features.file-upload.post-method.php */
    public function add(File $file): void
    {
        $this->fileList[] = $file;
    }

    public function toArray(): array
    {
        return $this->fileList;
    }
}