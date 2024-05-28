<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

class FileSet
{
    /** @var array<int,File> */
    private array $fileList = [];

    /** @see https://www.php.net/manual/pt_BR/features.file-upload.post-method.php */
    public function add(File $file): void
    {
        $this->fileList[] = $file;
    }

    /** @return array<int,File> */
    public function toArray(): array
    {
        return $this->fileList;
    }

    public function __toString(): string
    {
        $paramList = array_map(
            fn(File $item) => $item->getName(),
            $this->fileList
        );

        return implode(';', $paramList);
    }
}
