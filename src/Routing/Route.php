<?php

declare(strict_types=1);

namespace Freep\Application\Routing;

use Closure;

class Route
{
    const ANY    = 'ANY';
    const DELETE = 'DELETE';
    const GET    = 'GET';
    const PATCH  = 'PATCH';
    const POST   = 'POST';
    const PUT    = 'PUT';

    private Closure|string $action = '';

    private string $method = 'GET';

    private string $module = '';

    /** @var array<int,string> */
    private array $nodes = [];

    /** @var array<string,int|string> */
    private array $params = [];

    private string $pattern = '/';

    private ?Policy $policy = null;

    public function forModule(string $moduleIdentifier): Route
    {
        $this->module = $moduleIdentifier;
        return $this;
    }

    public function policyBy(Policy $policy): Route
    {
        $this->policy = $policy;
        return $this;
    }
    
    public function usingAction(Closure|string $action): Route
    {
        $this->action = $action;
        return $this;
    }

    public function usingMethod(string $method): Route
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function usingPattern(string $pattern): Route
    {
        $this->pattern = trim($pattern, "/");
        return $this;
    }

    public function action(): Closure|string
    {
        return $this->action;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function module(): string
    {
        return $this->module;
    }

    public function params(): array
    {
        return $this->params;
    }

    public function pattern(): string
    {
        return $this->pattern;
    }

    public function policy(): ?Policy
    {
        return $this->policy;
    }

    public function matchTo(string $method, string $requestPath): bool
    {
        if ($this->method() !== Route::ANY && strtoupper($method) !== $this->method()) {
            return false;
        }

        $requestPath = trim($requestPath, "/");

        $this->nodes = $requestPath === '' ? [] : explode("/", $requestPath);

        $segments = explode("/", $this->pattern);

        if (count($segments) !== count($this->nodes)) {
            return false;
        }

        $allParams = [];
        foreach ($segments as $index => $name) {
            // variável
            if (str_starts_with($name, ":") === true) {
                $paramName = trim($name, ":");
                $allParams[$paramName] = $this->nodes[$index];
                continue;
            }

            // literal
            if ($name !== $this->nodes[$index]) {
                return false;
            }
        }

        $this->params = $allParams;

        return true;
    }
}

