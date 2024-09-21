<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\FrontController;

use Iquety\Application\IoEngine\ActionDescriptor;
use Iquety\Application\IoEngine\FrontController\Command\Command;
use Iquety\Application\IoEngine\Action\Input;

class CommandSource
{
    public function __construct(private string $namespace)
    {
    }

    public function getIdentity(): string
    {
        return md5($this->namespace);
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getDescriptorTo(string $bootstrapClass, Input $input): ?ActionDescriptor
    {
        $input->reset();

        return $this->processUriLevel(
            $bootstrapClass,
            $input
        );
    }

    private function processUriLevel(string $bootstrapClass, Input $input): ?ActionDescriptor
    {
        $className = $this->namespace
            . "\\"
            . $this->makeNamespaceFrom($input);

        if (class_exists($className) === true) {
            return new ActionDescriptor(
                Command::class,
                $bootstrapClass,
                $className,
                'execute'
            );
        }

        if ($input->hasNext() === true) {
            $input->next();

            return $this->processUriLevel($bootstrapClass, $input);
        }

        return null;
    }

    private function makeNamespaceFrom(Input $input): string
    {
        $nodeList = [];

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
