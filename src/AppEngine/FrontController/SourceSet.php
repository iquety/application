<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use InvalidArgumentException;
use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\Action\Input;

class SourceSet
{
    /** @var array<string,Source> */
    private array $sourceList = [];

    public function __construct(private string $bootstrapClass)
    {
    }

    public function add(Source $source): void
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
            $descriptor = $source->getDescriptorTo($this->bootstrapClass, $input);

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
