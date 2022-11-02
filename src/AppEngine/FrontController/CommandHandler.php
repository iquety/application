<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use RuntimeException;

class CommandHandler
{
    private string $currentCommand = '';

    private string $currentModule = 'all';

    private array $currentParams = [];
    
    /** @var array<string,string> */
    private array $namespaceList = [];

    private bool $notFound = true;

    public function addNamespace(string $moduleIdentifier, string $commandsNamespace): void
    {
        $this->namespaceList[$moduleIdentifier] = $commandsNamespace;
    }

    public function namespaces(): array
    {
        return $this->namespaceList;
    }
    
    /** Obtém o comando atual */
    public function action(): string
    {
        return $this->currentCommand . '::execute';
    }

    /** Obtém o módulo do comando atual */
    public function module(): string
    {
        return $this->currentModule;
    }

    /** Obtém o módulo do comando atual */
    public function params(): array
    {
        return $this->currentParams;
    }

    public function commandNotFound(): bool
    {
        return $this->notFound;
    }

    public function process(string $method, string $path): void
    {
        if ($this->namespaces() === []) {
            throw new RuntimeException(
                'This bootstrap has no directories registered as command source'
            );
        }

        $pathNodes = explode('/', trim($path, '/'));

        $amountNodes = count($pathNodes);

        $potentialCommands = [];
        foreach($this->namespaces() as $moduleIdentifier => $namespace){
            
            for ($x = 0; $x < $amountNodes; $x++) {
                $name = $this->makeCommandName($namespace, $pathNodes, 1, $x);

                $potentialCommands[] = [
                    'module' => $moduleIdentifier,
                    'class' => $name
                ];
            }
    
            for ($x = 0; $x < $amountNodes-1; $x++) {
                $name = $this->makeCommandName($namespace, $pathNodes, 2, $x);

                $potentialCommands[] = [
                    'module' => $moduleIdentifier,
                    'class' => $name
                ];
            }
        }

        $this->resolveCommand($potentialCommands);

        // if (
        //     $command !== null
        //     && in_array($command->method(), [$method, Command::METHOD_ANY]) === true
        // ) {
        //     $this->currentCommand = $command;
        //     $this->notFound = false;
        // }
    }

    private function resolveCommand(array $potentialCommands): void
    {
        foreach($potentialCommands as $item) {
            $moduleIdentifier = $item['module'];

            $className = $item['class'];

            if(class_exists($className) === true) {
                $this->currentModule = $moduleIdentifier;
                $this->currentCommand = $className;
                $this->notFound = false;
                break;
            }
        }
    }

    private function makeCommandName(string $namespace, array $nodes, int $level, int $params = 0): string
    {
        $nodes = array_map(fn($value) => ucfirst($value), $nodes);

        $amountNodes = count($nodes);
        $level = $amountNodes < $level ? $amountNodes : $level;

        $directoryNodes = [];

        // extrai niveis de diretórios
        for ($x = 1; $x < $level; $x++) {
            $directoryNodes[] = array_shift($nodes);
        }

        // extrai parâmetros do final
        for ($x = 1; $x <= $params; $x++) {
            array_pop($nodes);
        }

        $base = implode('\\', $directoryNodes);
        $commandName = implode('', $nodes);

        return $base === ''
            ? $namespace . '\\' . $commandName
            : $namespace . '\\' . $base . '\\' . $commandName;
    }
}
