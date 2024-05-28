<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use Closure;
use Psr\Http\Message\UploadedFileInterface;

class File
{
    private string $name;

    private string $mimeType;

    private int $size;

    private Closure $lazyContent;

    private int $error = 0;

    public function __construct(private UploadedFileInterface $file)
    {
        $this->name        = $this->file->getClientFilename();
        $this->mimeType    = $this->file->getClientMediaType();
        $this->size        = $this->file->getSize();
        $this->lazyContent = fn() => $this->file->getStream()->getContents();
        $this->error       = $this->file->getError();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getContent(): string
    {
        $callback = $this->lazyContent;

        return $callback();
    }

    public function hasError(): bool
    {
        return $this->error !== UPLOAD_ERR_OK;
    }

    public function getErrorMessage(): string
    {
        return match ($this->error) {
            UPLOAD_ERR_OK         => 'There is no error, the file uploaded with success',
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE ' .
                                     'directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
        };
    }
}
