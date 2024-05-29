<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

/**
 * Transforma um URI em parâmetros de entrada normalizados
 */
class UriParser
{
    /** @var array<int|string,array<int|string,mixed>|float|int|string> */
    private array $paramList = [];

    /** @var array<int,string> */
    private array $path;

    public function __construct(string $string)
    {
        $uriData = parse_url($string);

        $this->path = $this->parsePath($uriData['path'] ?? '');

        $this->paramList = array_merge(
            $this->path,
            $this->parseQuery($uriData['query'] ?? '')
        );
    }

    /** @return array<int,string> */
    public function getPath(): array
    {
        return $this->path;
    }

    /** @return array<int|string,array<mixed>|float|int|string> */
    public function toArray(): array
    {
        return array_map(
            fn($value) => $this->fixTypes($value),
            $this->paramList
        );
    }

    /** @return array<int,string> */
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

    /** @return array<int|string,array<int|string,mixed>|string> */
    private function parseQuery(string $queryString): array
    {
        $queryParamList = [];

        parse_str($queryString, $queryParamList);

        return $queryParamList;
    }

    /**
     * @param array<mixed>|float|int|string $value
     * @return array<mixed>|float|int|string
     */
    public function fixTypes(array|float|int|string $value): array|float|int|string
    {
        if (is_array($value) === true) {
            foreach ($value as $key => $subValue) {
                $value[$key] = $this->fixTypes($subValue);
            }

            return $value;
        }

        if (is_numeric($value) === false) {
            return $value;
        }

        if (is_int($value + 0) === true) {
            return (int)$value;
        }

        return (float)$value;
    }
}
