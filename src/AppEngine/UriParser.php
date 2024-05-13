<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

class UriParser
{
    /** @var array<int|string,float|int|string> */
    private array $paramList = [];

    public function __construct(string $string)
    {
        $uriData = parse_url($string);

        $pathParams = $this->parsePath($uriData['path']);

        $queryParams = $this->parseQuery($uriData['query'] ?? '');

        $this->paramList = array_merge($pathParams, $queryParams);
    }

    public function toArray(): array
    {
        return $this->paramList;
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

        return $this->fixTypes(explode('/', $cleanedPath));
    }

    /** @return array<string,float|int|string> */
    private function parseQuery(string $queryString): array
    {
        $queryParamList = [];

        parse_str($queryString, $queryParamList);

        return $this->fixTypes($queryParamList);
    }

    /** 
     * @param array<int|string,float|int|string> $paramList 
     * @return array<int|string,float|int|string>
     */
    private function fixTypes(array $paramList): array
    {
        foreach ($paramList as $index => $value) {
            if (is_numeric($value) === false) {
                continue;
            }

            if (is_int($value + 0) === true) {
                $paramList[$index] = (int)$value;
                continue;
            }

            $paramList[$index] = (float)$value;
        }

        return $paramList;
    }
}

