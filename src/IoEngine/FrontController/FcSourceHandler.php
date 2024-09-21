<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\FrontController;

use InvalidArgumentException;
use Iquety\Application\IoEngine\ActionDescriptor;
use Iquety\Application\IoEngine\FrontController\Command\Command;
use Iquety\Application\IoEngine\FrontController\Command\ErrorCommand;
use Iquety\Application\IoEngine\FrontController\Command\MainCommand;
use Iquety\Application\IoEngine\FrontController\Command\NotFoundCommand;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\SourceHandler;
use RuntimeException;

class FcSourceHandler implements SourceHandler
{
    private string $errorCommandClass = ErrorCommand::class;

    private string $mainCommandClass = MainCommand::class;

    private string $notFoundCommandClass = NotFoundCommand::class;

    /** @var array<int,SourceSet> */
    private array $sourceList = [];

    public function addSources(CommandSourceSet $sourceSet): void
    {
        $this->sourceList[] = $sourceSet;
    }

    public function hasSources(): bool
    {
        foreach ($this->sourceList as $sourceSet) {
            if ($sourceSet->hasSources() === true) {
                return true;
            }
        }

        return false;
    }

    public function getDescriptorTo(Input $input): ?ActionDescriptor
    {
        if ($this->hasSources() === false) {
            throw new RuntimeException(
                'No registered sources for getting commands'
            );
        }

        if ($input->getPath() === []) {
            return $this->getMainDescriptor();
        }

        foreach ($this->getSourceList() as $sourceSet) {
            $descriptor = $sourceSet->getDescriptorTo($input);

            if ($descriptor !== null) {
                return $descriptor;
            }
        }

        return null;
    }

    public function getErrorDescriptor(): ActionDescriptor
    {
        return $this->makeDescriptor('error', $this->errorCommandClass, 'execute');
    }

    public function getMainDescriptor(): ActionDescriptor
    {
        return $this->makeDescriptor('main', $this->mainCommandClass, 'execute');
    }

    public function getNotFoundDescriptor(): ActionDescriptor
    {
        return $this->makeDescriptor('not-found', $this->notFoundCommandClass, 'execute');
    }

    /** @return array<int,SourceSet> */
    public function getSourceList(): array
    {
        return $this->sourceList;
    }

    public function setErrorActionClass(string $actionClass): self
    {
        $this->assertCommand($actionClass);

        $this->errorCommandClass = $actionClass;

        return $this;
    }

    public function setMainActionClass(string $actionClass): self
    {
        $this->assertCommand($actionClass);

        $this->mainCommandClass = $actionClass;

        return $this;
    }

    public function setNotFoundActionClass(string $actionClass): self
    {
        $this->assertCommand($actionClass);

        $this->notFoundCommandClass = $actionClass;

        return $this;
    }

    private function assertCommand(string $actionClass): void
    {
        if (is_subclass_of($actionClass, Command::class) === false) {
            throw new InvalidArgumentException("Class $actionClass is not a valid command");
        }
    }

    private function makeDescriptor(
        string $bootstrapClass,
        string $className,
        string $actionName
    ): ActionDescriptor {
        return new ActionDescriptor(
            Command::class,
            $bootstrapClass,
            $className,
            $actionName
        );
    }
}
