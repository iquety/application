<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

class CommandHandler
{
    /** @var array<string,DirectorySet> */
    private array $commandSourceList = [];

    /** @var array<int,string> */
    private array $pathNodes = [];

    private string $rootCommand = '';

    public function setRootCommand(string $commandIdentifier): void
    {
        $this->rootCommand = $commandIdentifier;
    }

    public function addModuleSources(string $moduleIdentifier, DirectorySet $directorySet): void
    {
        $this->commandSourceList[$moduleIdentifier] = $directorySet;
    }

    /** @return array<string,string> */
    public function commandSources(): array
    {
        return $this->commandSourceList;
    }

    /**
     * Devolve a lista de possíveis comandos
     * @return array<int,CommandPossibility>
     */
    public function process(string $path): array
    {
        if ($this->commandSources() === []) {
            return [];
        }

        $path = $this->sanitizePath($path);

        if ($path === '') {
            return [];
        }

        return $this->resolvePossibilities($path);
    }

    private function sanitizePath(string $path): string
    {
        $info = (array)parse_url(trim($path, '/'));

        if (isset($info['path']) === false) {
            return '';
        }

        return trim($info['path'], '/');
    }

    // /** @return array<int,string> */
    // private function extractPathNodes(string $path): array
    // {
    //     $pathNodes = explode('/', $path);

    //     return $pathNodes[0] === "" ? [] : $pathNodes;
    // }

    // /** @return array<int,CommandPossibility> */
    // private function resolvePossibilities(string $uri): array
    // {
    //     $potentialCommands = [];

    //     /** @var DirectorySet $directorySet */
    //     foreach ($this->namespaces() as $moduleIdentifier => $directorySet) {

    //         var_dump($directorySet->getCommand($uri));
    //         exit;

    //         $amountNodes = count($this->pathNodes);

    //         var_dump($this->namespaces(), $amountNodes, $this->pathNodes);
    //         exit;

    //         for ($x = 0; $x < $amountNodes; $x++) {
    //             $potentialCommands[] =
    //                 $this->makePossibility($moduleIdentifier, $namespace, $amountNodes, 1, $x);
    //         }

    //         for ($x = 0; $x < $amountNodes - 1; $x++) {
    //             $potentialCommands[] =
    //                 $this->makePossibility($moduleIdentifier, $namespace, $amountNodes, 2, $x);
    //         }
    //     }

    //     return $potentialCommands;
    // }

    public function resolveCommand(string $path): ?CommandDescriptor
    {
        $path = $this->sanitizePath($path);

        if ($path === '') {
            return null;
        }

        $potentialCommands = [];

        if ($path === '' && $potentialCommands === [] && $this->rootCommand !== '') {
            return new CommandDescriptor(
                '',
                $this->rootCommand,
                []
            );
        }

        foreach ($potentialCommands as $possibility) {
            if (class_exists($possibility->callable()) === true) {
                return new CommandDescriptor(
                    $possibility->module(),
                    $possibility->callable(),
                    $possibility->params()
                );
            }
        }

        return null;
    }

    private function makePossibility(
        string $moduleIdentifier,
        string $namespace,
        int $amountNodes,
        int $level,
        int $params = 0
    ): CommandPossibility {
        $nodes = $this->pathNodes;

        $level = $amountNodes < $level ? $amountNodes : $level;

        $directoryNodes = [];
        $paramNodes = [];

        // extrai niveis de diretórios
        for ($x = 1; $x < $level; $x++) {
            $directoryNodes[] = (string)array_shift($nodes);
        }

        // extrai parâmetros do final
        for ($x = 1; $x <= $params; $x++) {
            $paramNodes[] = (string)array_pop($nodes);
        }

        $directoryNodes = array_map(fn($value) => ucfirst($value), $directoryNodes);
        $nodes = array_map(fn($value) => ucfirst($value), $nodes);

        $base = implode('\\', $directoryNodes);
        $commandName = implode('', $nodes);

        $callable = $base === ''
            ? $namespace . '\\' . $commandName
            : $namespace . '\\' . $base . '\\' . $commandName;

        // inverte a ordem  dos parâmetros
        krsort($paramNodes);

        // reindexa os itens
        $paramNodes = array_values($paramNodes);

        // ajusta os tipos

        return new CommandPossibility($moduleIdentifier, $callable, $this->fixTypes($paramNodes));
    }

    /**
     * @param array<int,string> $params
     * @return array<int,string|int|float>
     */
    private function fixTypes(array $params): array
    {
        foreach ($params as $index => $value) {
            if (is_numeric($value) === false) {
                continue;
            }

            if (is_int($value + 0) === true) {
                $params[$index] = (int)$value;
                continue;
            }

            $params[$index] = (float)$value;
        }

        return $params;
    }
}
