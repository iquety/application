<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use InvalidArgumentException;
use Iquety\Application\AppEngine\FrontController\Command\Command;
use Iquety\Application\AppEngine\FrontController\Command\CommandDescriptor;
use Iquety\Application\AppEngine\FrontController\Command\ErrorCommand;
use Iquety\Application\AppEngine\FrontController\Command\MainCommand;
use Iquety\Application\AppEngine\FrontController\Command\NotFoundCommand;
use RuntimeException;

class SourceHandler
{
    private bool $hasSources = false;

    private string $errorCommandClass = ErrorCommand::class;

    private string $mainCommandClass = MainCommand::class;

    private string $notFoundCommandClass = NotFoundCommand::class;

    /** @var array<int,DirectorySet> */
    private array $sourceList = [];

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
            return new CommandDescriptor('', $this->mainCommandClass, []);
        }

        foreach ($this->getSourceList() as $directorySet) {
            $descriptor = $directorySet->getDescriptorTo($uri);

            if ($descriptor !== null) {
                return $descriptor;
            }
        }

        return new CommandDescriptor('', $this->notFoundCommandClass, []);
    }

    public function getErrorDescriptor(): CommandDescriptor
    {
        return new CommandDescriptor('', $this->errorCommandClass, []);
    }

    public function getMainDescriptor(): CommandDescriptor
    {
        return new CommandDescriptor('', $this->mainCommandClass, []);
    }

    public function getNotFoundDescriptor(): CommandDescriptor
    {
        return new CommandDescriptor('', $this->notFoundCommandClass, []);
    }

    /** @return array<int,DirectorySet> */
    public function getSourceList(): array
    {
        return $this->sourceList;
    }

    public function setErrorCommandClass(string $commandClass): self
    {
        if ($this->errorCommandClass === $commandClass) {
            return $this;
        }

        $this->assertCommand($commandClass);

        $this->errorCommandClass = $commandClass;

        return $this;
    }

    public function setMainCommandClass(string $commandClass): self
    {
        if ($this->mainCommandClass === $commandClass) {
            return $this;
        }

        $this->assertCommand($commandClass);

        $this->mainCommandClass = $commandClass;

        return $this;
    }

    public function setNotFoundCommandClass(string $commandClass): self
    {
        if ($this->notFoundCommandClass === $commandClass) {
            return $this;
        }

        $this->assertCommand($commandClass);
        
        $this->notFoundCommandClass = $commandClass;

        return $this;
    }

    private function assertCommand(string $commandClass): void
    {
        if (is_subclass_of($commandClass, Command::class) === false) {
            throw new InvalidArgumentException("Class $commandClass is not a valid command");
        }
    }
}
