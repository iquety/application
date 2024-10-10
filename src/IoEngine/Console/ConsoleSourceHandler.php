<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Console;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Action\ActionDescriptor;
use Iquety\Application\IoEngine\SourceHandler;

class ConsoleSourceHandler implements SourceHandler
{
    private string $scriptName = 'unknown';

    private string $scriptPath = '';

    /** @var array<int,string> */
    private array $directoryList = [];

    private string $moduleClass = '';

    public function addSources(RoutineSourceSet $sourceSet): void
    {
        /** @var RoutineSource $source */
        foreach ($sourceSet->toArray() as $source) {
            $this->directoryList[] = $source->getDirectory();
        }

        $this->moduleClass = $sourceSet->getModuleClass();
    }

    public function hasSources(): bool
    {
        return $this->directoryList !== [];
    }

    public function getScriptName(): string
    {
        return $this->scriptName;
    }

    public function getScriptPath(): string
    {
        return $this->scriptPath;
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function getDescriptorTo(Input $input): ?ActionDescriptor
    {
        return ConsoleDescriptor::factory($this->moduleClass, '', 0);
    }

    /** @return array<int,string> */
    public function getDirectoryList(): array
    {
        return $this->directoryList;
    }

    public function setScriptName(string $scriptName): self
    {
        $this->scriptName = $scriptName;

        return $this;
    }

    public function setScriptPath(string $scriptPath): self
    {
        $this->scriptPath = $scriptPath;

        return $this;
    }

    public function getErrorDescriptor(): ActionDescriptor
    {
        throw new NotImplementedException(
            'The Console engine does not use this method'
        );
    }

    public function getMainDescriptor(): ActionDescriptor
    {
        throw new NotImplementedException(
            'The Console engine does not use this method'
        );
    }

    public function getNotFoundDescriptor(): ActionDescriptor
    {
        throw new NotImplementedException(
            'The Console engine does not use this method'
        );
    }

    public function setErrorActionClass(string $actionClass): self
    {
        throw new NotImplementedException(
            'The Console engine does not use this method'
        );
    }

    public function setMainActionClass(string $actionClass): self
    {
        throw new NotImplementedException(
            'The Console engine does not use this method'
        );
    }

    public function setNotFoundActionClass(string $actionClass): self
    {
        throw new NotImplementedException(
            'The Console engine does not use this method'
        );
    }
}
