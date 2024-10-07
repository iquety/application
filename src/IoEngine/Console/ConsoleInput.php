<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Console;

class ConsoleInput
{
    /** @param array<int,string> $argumentList */
    public function __construct(private array $argumentList)
    {
    }

    /** @return array<int,string> */
    public function toArray(): array
    {
        return $this->argumentList;
    }
}
