<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\FrontController;

use InvalidArgumentException;
use Iquety\Application\IoEngine\ActionDescriptor;
use Iquety\Application\IoEngine\Action\Input;

class CommandSourceSet
{
    /** @var array<string,Source> */
    private array $sourceList = [];

    public function __construct(private string $moduleClass)
    {
    }

    public function add(CommandSource $source): void
    {
        $index = $source->getIdentity();

        if (isset($this->sourceList[$index]) === true) {
            throw new InvalidArgumentException(
                'The specified source already exists'
            );
        }

        $this->sourceList[$index] = $source;
    }

    public function getDescriptorTo(Input $input): ?ActionDescriptor
    {
        foreach ($this->sourceList as $source) {
            $descriptor = $source->getDescriptorTo($this->moduleClass, $input);

            if ($descriptor !== null) {
                return $descriptor;
            }
        }

        return null;
    }

    public function hasSources(): bool
    {
        return $this->sourceList !== [];
    }

    /** @return array<string,Source> */
    public function toArray(): array
    {
        return $this->sourceList;
    }
}
