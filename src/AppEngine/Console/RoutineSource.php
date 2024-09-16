<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Console;

use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\Console\Script;

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
