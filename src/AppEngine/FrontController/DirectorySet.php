<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use InvalidArgumentException;

class DirectorySet
{
    /** @var array<string,Directory> */
    private array $directoryList = [];

    public function __construct(private string $bootstrapClass)
    {
    }

    public function add(Directory $directory): void
    {
        $index = $directory->getIdentity();

        if (isset($this->directoryList[$index]) === true) {
            throw new InvalidArgumentException(
                'The specified directory already exists'
            );
        }

        $this->directoryList[$index] = $directory;
    }

    public function getDescriptorTo(string $uri): ?CommandDescriptor
    {
        foreach($this->directoryList as $directory) {
            $descriptor = $directory->getDescriptorTo($this->bootstrapClass, $uri);

            if ($descriptor !== null) {
                return $descriptor;
            }
        }

        return null;
    }

    /** @var array<string,Directory> */
    public function toArray(): array
    {
        return $this->directoryList;
    }
}
