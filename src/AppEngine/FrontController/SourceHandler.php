<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use RuntimeException;

class SourceHandler
{
    private bool $hasSources = false;
    
    private string $errorCommandClass = '';

    private string $notFoundCommandClass = '';

    private string $rootCommandClass = '';
    
    /** @var array<string,DirectorySet> */
    private array $sourceList = [];

    public function getErrorDescriptor(): CommandDescriptor
    {
        return new CommandDescriptor('', $this->errorCommandClass, []);
    }

    public function setErrorCommand(string $commandClass): self
    {
        if ($commandClass === ErrorCommand::class) {
            return $this;
        }

        $this->errorCommandClass = $commandClass;

        return $this;
    }

    public function setNotFoundCommand(string $commandClass): self
    {
        if ($commandClass === NotFoundCommand::class) {
            return $this;
        }

        $this->notFoundCommandClass = $commandClass;

        return $this;
    }

    public function setRootCommand(string $commandClass): self
    {
        if ($commandClass === DefaultCommand::class) {
            return $this;
        }

        $this->rootCommandClass = $commandClass;

        return $this;
    }

    public function addSources(DirectorySet $directorySet): void
    {
        if ($directorySet->toArray() !== []) {
            $this->hasSources = true;
        }

        $this->sourceList[] = $directorySet;
    }

    public function getDescriptorTo(string $uri): ?CommandDescriptor
    {
        if ($this->hasSources === false) {
            throw new RuntimeException(
                'No directories registered as command source'
            );
        }

        $uri = trim(trim($uri), '/');

        if ($uri === '') {
            return new CommandDescriptor('', $this->rootCommandClass, []);
        }

        foreach($this->sourceList as $directorySet) {
            $descriptor = $directorySet->getDescriptorTo($uri);

            if ($descriptor !== null) {
                return $descriptor;
            }
        }
        
        return new CommandDescriptor('', $this->notFoundCommandClass, []);
    }
}
