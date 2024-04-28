<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

class CommandDescriptor
{
    /** @param array<int,string|int|float> $params */
    public function __construct(
        private string $moduleIdentifier,
        private string $callable,
        private array $params
    ) {
    }

    public function action(): string
    {
        return $this->callable . '::execute';
    }

    public function module(): string
    {
        return $this->moduleIdentifier;
    }

    /** @return array<int,string|int|float> */
    public function params(): array
    {
        return $this->params;
    }
}
