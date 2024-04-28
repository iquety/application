<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use InvalidArgumentException;
use RuntimeException;

class Directory
{
    private string $lastUri = '';

    private string $lastClassName = '';

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

    /** 
     * Devolve a última uri usada no método getCommandTo
     */
    public function getLastUri(): string
    {
        return $this->lastUri;
    }

    /** 
     * Devolve o nome da última classe procurada no método getCommandTo
     */
    public function getLastClassName(): string
    {
        return $this->lastClassName;
    }

    public function getCommandTo(string $uri): ?Command
    {
        $this->lastUri = trim(trim($uri), '/');

        $className = $this->namespace 
            . "\\"
            . $this->makeNamespaceFrom($this->lastUri);

        $this->lastClassName = $className;

        if (class_exists($className) === false) {
            return null;
        }
        
        return new $className();
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
}
