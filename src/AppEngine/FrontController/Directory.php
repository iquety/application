<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use InvalidArgumentException;

class Directory
{
    /** @var array<string,string|int|float> */
    private array $paramList = [];

    public function __construct(private string $namespace, private string $fullPath)
    {
        $fullPath = realpath($fullPath);

        if ($fullPath === false) {
            throw new InvalidArgumentException(
                'The directory specified for commands does not exist'
            );
        }

        $this->fullPath = $fullPath;
    }

    public function getIdentity(): string
    {
        return md5($this->namespace . $this->fullPath);
    }

    public function getDescriptorTo(string $bootstrapClass, string $uri): ?CommandDescriptor
    {
        $cleanedUri = $this->sanitizePath($uri);

        if ($cleanedUri === '') {
            return null;
        }

        $nodeList = explode('/', $cleanedUri);

        return $this->processUriLevel($bootstrapClass, $nodeList);
    }

    private function processUriLevel(string $bootstrapClass, array &$nodeList): ?CommandDescriptor
    {
        if ($nodeList === []) {
            return null;
        }

        $uri = implode('/', $nodeList);

        $className = $this->namespace 
            . "\\"
            . $this->makeNamespaceFrom($uri);

        if (class_exists($className) === true) {
            return new CommandDescriptor(
                $bootstrapClass,
                $className,
                $this->fixParams($this->paramList)
            );
        }

        $this->paramList[] = (string)array_pop($nodeList);

        return $this->processUriLevel($bootstrapClass, $nodeList);
    }

    private function sanitizePath(string $uri): string
    {
        $cleanedUri = trim($uri);
        $cleanedUri = trim($cleanedUri, '/');

        $info = (array)parse_url($cleanedUri);

        if (isset($info['path']) === false) {
            return '';
        }

        return trim($info['path'], '/');
    }

    private function makeNamespaceFrom(string $uri): string
    {
        $nodeList = explode('/', $uri);

        foreach($nodeList as $index => $nodePath) {
            $nodeList[$index] = $this->makeCamelCase($nodePath);
        }

        return implode("\\", $nodeList);
    }

    private function makeCamelCase(string $nodePath): string
    {
        $camelCase = explode('-', $nodePath);

        $camelCase = array_map(
            fn($word) => ucfirst(mb_strtolower($word)),
            $camelCase
        );

        return implode('', $camelCase);
    }

    /**
     * @param array<int,string> $paramList
     * @return array<int,string|int|float>
     */
    private function fixParams(array $paramList): array
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

        // inverte a ordem dos parâmetros
        // para manter a ordem de aparição no URI
        krsort($paramList);

        return array_values($paramList);
    }
}
