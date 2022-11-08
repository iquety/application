<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

class Input
{
    public function __construct(private array $params)
    {
    }

    public function first(): mixed
    {
        return current($this->params);
    }

    public function param(int $index): mixed
    {
        return $this->params[$index] ?? null; 
    }

    public function __toString(): string
    {
        return implode(',', $this->params);
    }
}
