<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Console;

use Iquety\Application\IoEngine\ActionDescriptor;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Console\Script;

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

    public function getDescriptorTo(string $bootstrapClass, Input $input): ?ActionDescriptor
    {
        $input->reset();

        return new ActionDescriptor(
            Script::class,
            $bootstrapClass,
            '',
            ''
        );
    }
}
