<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Console;

class ConsoleInput
{
    public function __construct(private array $argumentList)
    {
    }

    public function toArray(): array
    {
        return $this->argumentList;
    }
}
