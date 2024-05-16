<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

/**
 * Transforma um URI em parâmetros de entrada normalizados
 */
class UriParser
{
    /** @var array<int|string,float|int|string> */
    private array $paramList = [];

    /** @var array<int,string> */
    private array $path;

    public function __construct(string $string)
    {
        $uriData = parse_url($string);

        $pathParams = $this->parsePath($uriData['path'] ?? '');

        $this->path = $pathParams;

        $queryParams = $this->parseQuery($uriData['query'] ?? '');

        $this->paramList = array_merge($pathParams, $queryParams);
    }

    /** @return array<int,string> */
    public function getPath(): array
    {
        return $this->path;
    }

    public function toArray(): array
    {
        return array_map(
            fn($value) => $this->fixTypes($value),
            $this->paramList
        );
    }

    /** @return array<int,float|int|string> */
    private function parsePath(string $path): array
    {
        $cleanedPath = trim($path);
        $cleanedPath = trim($cleanedPath, '/');
        $cleanedPath = trim($cleanedPath);

        if ($cleanedPath === '') {
            return [];
        }

        return explode('/', $cleanedPath);
    }

    /** @return array<string,float|int|string> */
    private function parseQuery(string $queryString): array
    {
        $queryParamList = [];

        parse_str($queryString, $queryParamList);

        return $queryParamList;
    }

    private function fixTypes(string $value): float|int|string
    {
        if (is_numeric($value) === false) {
            return $value;
        }

        if (is_int($value + 0) === true) {
            return (int)$value;
        }

        return (float)$value;
    }
}
