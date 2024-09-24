<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Console;

class ConsoleOutput
{
    public function __construct(private string $output)
    {
    }

    public function getBody(): string
    {
        return $this->output;
    }
}
