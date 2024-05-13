<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use Psr\Http\Message\UploadedFileInterface;

class File
{
    public function __construct(private UploadedFileInterface $file)
    {
    }

    public function getName(): string
    {
        return $this->file->getClientFilename();
    }

    public function getMimeType(): string
    {
        return $this->file->getClientMediaType();
    }

    public function getSize(): int
    {
        return $this->file->getSize();
    }

    public function getContent(): string
    {
        return $this->file->getStream()->getContents();
    }

    public function hasError(): bool
    {
        return $this->file->getError() !== UPLOAD_ERR_OK;
    }

    public function getErrorMessage(): string
    {
        return match($this->file->getError()) {
            UPLOAD_ERR_OK         => 'There is no error, the file uploaded with success',
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
        };
    }
}