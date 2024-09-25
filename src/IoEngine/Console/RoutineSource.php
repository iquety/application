<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Console;

class RoutineSource
{
    public function __construct(private string $directory)
    {
    }

    public function getIdentity(): string
    {
        return md5($this->directory);
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }
}
