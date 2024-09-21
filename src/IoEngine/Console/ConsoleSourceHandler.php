<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Console;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\ActionDescriptor;
use Iquety\Application\IoEngine\SourceHandler;
use Iquety\Console\Routine;
use Iquety\Injection\Container;

class ConsoleSourceHandler implements SourceHandler
{
    private string $commandName = 'unknown';

    private string $commandPath = '';

    /** @var array<int,string> */
    private array $directoryList = [];

    private string $bootstrapClass = '';

    public function addSources(RoutineSourceSet $sourceSet): void
    {
        /** @var RoutineSource $source */
        foreach($sourceSet->toArray() as $source) {
            $this->directoryList[] = $source->getDirectory();
        }

        $this->bootstrapClass = $sourceSet->getBootstrapClass();
    }

    public function hasSources(): bool
    {
        return $this->directoryList !== [];
    }

    public function getCommandName(): string
    {
        return $this->commandName;
    }

    public function getCommandPath(): string
    {
        return $this->commandPath;
    }

    public function getDescriptorTo(Input $input): ?ActionDescriptor
    {
        return new ActionDescriptor(
            Routine::class,
            $this->bootstrapClass,
            '',
            ''
        );
    }

    /** @return array<int,string> */
    public function getDirectoryList(): array
    {
        return $this->directoryList;
    }

    public function setCommandName(string $commandName): self
    {
        $this->commandName = $commandName;

        return $this;
    }

    public function setCommandPath(string $commandPath): self
    {
        $this->commandPath = $commandPath;

        return $this;
    }

    public function getErrorDescriptor(): ActionDescriptor
    {
        throw new InvalidArgumentException(
            'The Console engine does not use this method'
        );
    }

    public function getMainDescriptor(): ActionDescriptor
    {
        throw new InvalidArgumentException(
            'The Console engine does not use this method'
        );
    }

    public function getNotFoundDescriptor(): ActionDescriptor
    {
        throw new InvalidArgumentException(
            'The Console engine does not use this method'
        );
    }

    public function setErrorActionClass(string $actionClass): self
    {
        throw new InvalidArgumentException(
            'The Console engine does not use this method'
        );

        return $this;
    }

    public function setMainActionClass(string $actionClass): self
    {
        throw new InvalidArgumentException(
            'The Console engine does not use this method'
        );
        
        return $this;
    }

    public function setNotFoundActionClass(string $actionClass): self
    {
        throw new InvalidArgumentException(
            'The Console engine does not use this method'
        );
        
        return $this;
    }
}
