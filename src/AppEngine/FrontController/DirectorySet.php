<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use InvalidArgumentException;

class DirectorySet
{
    /** @var array<string,Directory> */
    private array $directoryList = [];

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

    public function getCommandTo(string $uri): ?Command
    {
        foreach($this->directoryList as $directory) {
            $command = $directory->getCommandTo($uri);

            if ($command !== null) {
                return $command;
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
