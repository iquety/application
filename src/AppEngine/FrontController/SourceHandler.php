<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use InvalidArgumentException;
use Iquety\Application\AppEngine\FrontController\Command\Command;
use Iquety\Application\AppEngine\FrontController\Command\CommandDescriptor;
use Iquety\Application\AppEngine\FrontController\Command\ErrorCommand;
use Iquety\Application\AppEngine\FrontController\Command\MainCommand;
use Iquety\Application\AppEngine\FrontController\Command\NotFoundCommand;
use Iquety\Application\AppEngine\Input;
use RuntimeException;

class SourceHandler
{
    private string $errorCommandClass = ErrorCommand::class;

    private string $mainCommandClass = MainCommand::class;

    private string $notFoundCommandClass = NotFoundCommand::class;

    /** @var array<int,DirectorySet> */
    private array $sourceList = [];

    public function addSources(DirectorySet $directorySet): void
    {
        $this->sourceList[] = $directorySet;
    }

    public function hasSources(): bool
    {
        foreach($this->sourceList as $directorySet) {
            if ($directorySet->hasDirectories() === true) {
                return true;
            }
        }
        
        return false;
    }

    public function getDescriptorTo(Input $input): ?CommandDescriptor
    {
        if ($this->hasSources() === false) {
            throw new RuntimeException(
                'No directories registered as command source'
            );
        }

        if ($input->getPath() === []) {
            return $this->getMainDescriptor($input);
        }

        foreach ($this->getSourceList() as $directorySet) {
            $descriptor = $directorySet->getDescriptorTo($input);

            if ($descriptor !== null) {
                return $descriptor;
            }
        }

        return null;
    }

    public function getErrorDescriptor(Input $input): CommandDescriptor
    {
        return new CommandDescriptor('error', $this->errorCommandClass, $input);
    }

    public function getMainDescriptor(Input $input): CommandDescriptor
    {
        return new CommandDescriptor('main', $this->mainCommandClass, $input);
    }

    public function getNotFoundDescriptor(Input $input): CommandDescriptor
    {
        return new CommandDescriptor('not-found', $this->notFoundCommandClass, $input);
    }

    /** @return array<int,DirectorySet> */
    public function getSourceList(): array
    {
        return $this->sourceList;
    }

    public function setErrorCommandClass(string $commandClass): self
    {
        $this->assertCommand($this->errorCommandClass, $commandClass);

        $this->errorCommandClass = $commandClass;

        return $this;
    }

    public function setMainCommandClass(string $commandClass): self
    {
        $this->assertCommand($commandClass);

        $this->mainCommandClass = $commandClass;

        return $this;
    }

    public function setNotFoundCommandClass(string $commandClass): self
    {
        $this->assertCommand($commandClass);
        
        $this->notFoundCommandClass = $commandClass;

        return $this;
    }

    private function assertCommand(string $newCommandClass): void
    {
        if (is_subclass_of($newCommandClass, Command::class) === false) {
            throw new InvalidArgumentException("Class $newCommandClass is not a valid command");
        }
    }
}
