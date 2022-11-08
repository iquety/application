<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

class CommandPossibility
{
    public function __construct(
        private string $moduleIdentifier,
        private string $callable,
        private array $params = []
    ) {
    }

    public function module(): string
    {
        return $this->moduleIdentifier;
    }

    public function callable(): string
    {
        return $this->callable;
    }

    public function params(): array
    {
        return $this->params;
    }
}
