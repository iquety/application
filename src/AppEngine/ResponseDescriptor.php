<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

class ResponseDescriptor
{
    public function __construct(
        private int $statusCode,
        private string $content
    ) {
    }

    public function getStatus(): int
    {
        return $this->statusCode;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}