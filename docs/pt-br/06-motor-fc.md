# Motor FrontController

[◂ Motor MVC](05-motor-mvc.md) | [Índice da documentação](indice.md) | [Motor Console ▸](07-motor-console.md)
-- | -- | --

## 1. Introdução

Basicamente o Front Controller é composto por um Manipulador Web (um controlador
único) que recebe todas as solicitações do usuário. Também existe uma hierarquia
de classes onde cada uma delas representa uma ação a ser executada (objetos de comando).

Quando, por exemplo, o usuário fizer uma solicitação `/usuario/editar/22`, o Manipulador Web
irá procurar na hierarquia de comandos. Se a classe Usuario/Editar for encontrada,
ela será usada para prover uma resposta para o usuário.

O funcionamento é muito semelhante ao MVC, porém, tende a prover uma Separação de
Preocupações (SOC) ainda melhor e mais definida.

## 2. Bootstrap

No arquivo de bootstrap do sistema (ver [Criando uma aplicação](docs/pt-br/01-instanciando.md)),
deve-se implementar a inicialização de uma aplicação que use o motor `FcEngine`:

```php
<?php

declare(strict_types=1);

use Iquety\Application\Application;
use Iquety\Application\IoEngine\FrontController\FcEngine;
use Iquety\Http\Adapter\HttpFactory\DiactorosHttpFactory;

ini_set('display_errors', 1);
error_reporting(E_ALL);

require dirname(__DIR__) . '/vendor/autoload.php';

$app = Application::instance();

$app->bootEngine(new FcEngine());

// registramos a instância do módulo principal
$app->bootApplication(...);

// registramos as instâncias de um ou mais módulos secundários
$app->bootModule(...);

$request = new DiactorosHttpFactory();

$response = $app->run($request->createRequestFromGlobals());

$app->sendResponse($response);
```

## 3. Implementação do Módulo

Agora que a inicialização da aplicação está implementada, precisamos fornecer a
instância de nosso módulo para o método `bootApplication` (se for o módulo principal)
ou `bootModule` (se for um módulo secundário). Para fins didáticos, Vamos chamar
nosso módulo de `MeuModuloFc`:

```php
class MeuModuloFc extends FcModule
{
    public function bootDependencies(Container $container): void
    {
        // dependência obrigatório para FrontController
        $container->addSingleton(Session::class, MemorySession::class);

        // dependência obrigatório para FrontController
        $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());

        // dependência adicional
        $container->addFactory(MinhaInterface::class, new MinhaImplementacao());
    }

    public function bootNamespaces(SourceSet &$sourceSet): void
    {
        // determina que os comandos serão procurados neste namespace
        $sourceSet->add(new Source('Acme\Meus\Comandos\Aqui'));
    }
}
```

## 5. Implementação do Comando

A última coisa a fazer, é criar comandos para serem executados. Na configuração
do `MeuModuloFc`, determinou-se que quando uma requisição for feita, um comando
correspondente será procurado no namespace `Acme\Meus\Comandos\Aqui`. A seguir,
implementaremos o arquivo `MeuComando.php`:

```php
<?php

declare(strict_types=1);

namespace Acme\Meus\Comandos\Aqui;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\FrontController\Command\Command;
use Iquety\Http\HttpMethod;

class MeuComando extends Command
{
    // A Injeção de Dependências irá procurar por 
    // uma dependência identificada como MinhaInterface::class
    // se tiver sido registrada em MeuModuloMvc::bootDependencies
    // então será disponibilizada para o argumento $dep
    public function __construct(MinhaInterface $dep)
    {
    }

    // O argumento $nomeQualquer receberá a Injeção de Dependências
    public function execute(Input $input, MinhaInterface $nomeQualquer): string
    {
        // o comando MeuComando somente continuará a execução se 
        // o verbo da requisição for POST
        $this->forMethod(HttpMethod::POST);

        return 'Resposta em texto';
    }
}
```

> **Injeção de Dependências:** para mais informações sobre o assunto, veja [arquitetura hexagonal](08-arquitetura-hexagonal.md).

Diferente do motor MVC, onde um Controlador pode possuir várias ações, um Comando
possui sempre uma única ação chamada `execute`.

Outra diferença é que, como não existe um roteador, será necessário especificar
qual verbo HTTP estará habilitado a executar o comando. Isso é feito na implementação
do comando, usando o método `forMethod` para definir o verbo adequado.

[◂ Motor MVC](05-motor-mvc.md) | [Índice da documentação](indice.md) | [Motor Console ▸](07-motor-console.md)
-- | -- | --
