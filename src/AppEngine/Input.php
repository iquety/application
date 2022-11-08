<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

class Input
{
    private array $params = [];

    private array $indexParams = [];

    public function __construct(array $params)
    {
        $this->params = $params;

        foreach (array_keys($params) as $index) {
            $this->indexParams[] = $index;
        }
    }

    public function first(): mixed
    {
        $index = $this->indexParams[0];
        return $this->params[$index];
    }

    public function param(int $index): mixed
    {
        if (isset($this->indexParams[$index]) === false) {
            return null;
        }

        $index = $this->indexParams[$index];

        return $this->params[$index] ?? null;
    }

    public function __toString(): string
    {
        return implode(',', $this->params);
    }
}
