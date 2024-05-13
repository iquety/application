<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Action;

class Input
{
    /** @var array<int|string,int|string|int|float> */
    private array $params = [];

    /** @var array<int,int|string> */
    private array $indexParams = [];

    // TODO deverá conter o corpo original da solicitação
    // json, xml, text etc
    private string $payload = '';

    /** @param array<int|string|int|float> $params */
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
        return http_build_query($this->params);
    }
}
