<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use InvalidArgumentException;
use Iquety\Application\AppEngine\FrontController\Command\CommandDescriptor;
use Iquety\Application\AppEngine\Input;

class Directory
{
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

    public function getDescriptorTo(string $bootstrapClass, Input $input): ?CommandDescriptor
    {
        return $this->processUriLevel(
            $bootstrapClass,
            $input
        );
    }

    private function processUriLevel(string $bootstrapClass, Input $input): ?CommandDescriptor
    {
        if ($input->hasNext() === false) {
            return null;
        }

        $className = $this->namespace
            . "\\"
            . $this->makeNamespaceFrom($input);

        if (class_exists($className) === true) {
            return new CommandDescriptor(
                $bootstrapClass,
                $className,
                $input
            );
        }

        $input->next();

        return $this->processUriLevel($bootstrapClass, $input);
    }

    private function makeNamespaceFrom(Input $input): string
    {
        foreach ($input->getTarget() as $index => $nodePath) {
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
