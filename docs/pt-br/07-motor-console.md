# Motor Console

[◂ Motor FrontController](06-motor-fc.md) | [Índice da documentação](indice.md) | [Arquitetura Hexagonal ▸](08-arquitetura-hexagonal.md)
-- | -- | --

## 1. Introdução

Este motor favorece a criação de aplicações para serem executadas no terminal.

## 2. Bootstrap

A primeira coisa a se fazer é criar um script de terminal e dar permissões de
execução:

```bash
touch exemplo
chmod a+x exemplo
```

Para executar, bastará entrar no terminal e digitar:

```bash
./exemplo
```

Claro que nada acontecerá, pois não há nada no script.

No arquivo do script, deve-se implementar a inicialização de uma aplicação que
use o motor `ConsoleEngine` como no exemplo abaixo:

```php
#!/bin/php
<?php

use Iquety\Application\Application;
use Iquety\Application\IoEngine\Console\ConsoleEngine;
use Iquety\Application\IoEngine\Console\ConsoleInput;
use Module\Console\MainConsole;

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

$application = Application::instance();

$application->bootEngine(new ConsoleEngine());

$application->bootApplication(...); // aqui colocaremos a instância do módulo

$output = $application->run(new ConsoleInput($argv));

$application->sendResponse($output);
```

## 3. Implementação do Módulo

Agora que o script está implementado, precisamos fornecer a instância de nosso
módulo para o método `bootApplication`. Para fins didáticos, Vamos chamar nosso
módulo de `MeuModuloConsole`:

```php
class MeuModuloConsole extends ConsoleModule
{
    public function bootDependencies(Container $container): void
    {
        // aqui registramos as dependências necessárias para o módulo
        $container->addFactory(MyInterface::class, new MyImplementation());
    }

    public function bootRoutineDirectories(RoutineSourceSet &$sourceSet): void
    {
        // o diretório onde estão as rotinas que o script será capaz de executar
        // pode-se especificar quantos diretórios forem necessários
        // neste exemplo, será especificado apenas um:
        $sourceSet->add(new RoutineSource(__DIR__ . '/Minhas/Rotinas/Aqui'));
    }

    public function getScriptName(): string
    {
        // este nome será usado para exibir informações ao usuário
        return 'meu-script';
    }

    public function getScriptPath(): string
    {
        // este caminho será usado para exibir informações ao usuário
        return __DIR__;
    }
}
```

Agora fornecemos o módulo para a aplicação:

```php
$application->bootApplication(new MeuModuloConsole());
```

## 4. Implementação da Rotina

A última coisa a fazer, é criar as rotinas para serem executadas. Na configuração
do `MeuModuloConsole`, determinou-se que as rotinas devem ser alocadas em
`__DIR__ . '/Minhas/Rotinas/Aqui'`. Para fins didáticos, criaremos um arquivo
neste diretório e chamaremos de `MinhaRotina.php`:

```php
<?php

declare(strict_types=1);

namespace Acme\Minhas\Rotinas\Aqui;

use Iquety\Application\IoEngine\Console\ConsoleRoutine;
use Iquety\Console\Arguments;

class MinhaRotina extends ConsoleRoutine
{
    protected function initialize(): void
    {
        $this->setName('rotina-linda');

        $this->setDescription('Informações lindas');
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    protected function handle(Arguments $arguments): void
    {
        $this->info('Uma rotina linda foi executada');
    }
}
```

## 5. Executando a Rotina

Agora, basta executar o script de terminal e especificar o nome da rotina:

```bash
./exemplo rotina-linda
➜ Uma rotina linda foi executada
```

Se o script for executado sem argumentos, as informações de ajuda serão exibidas:

```bash
./exemplo

How to use: 
  ./exemplo routine [options] [arguments]

Options: 
-h, --help            Display help information

Available routines: 
help                  Display help information
rotina-linda          Informações lindas
```

Para mais informações sobre como implementar uma rotina e sobre as ferramentas
disponíveis para utilização, leia a [documentação da biblioteca `iquety/console`](https://github.com/iquety/console).

[◂ Motor FrontController](06-motor-fc.md) | [Índice da documentação](indice.md) | [Arquitetura Hexagonal ▸](08-arquitetura-hexagonal.md)
-- | -- | --
