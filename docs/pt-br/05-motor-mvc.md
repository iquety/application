# Motor MVC

[◂ Motores](04-motores.md) | [Índice da documentação](indice.md) | [Motor FrontController ▸](06-motor-fc.md)
-- | -- | --

## 1. Introdução

O MVC (sigla de Model, View e Controller) é um padrão arquitetural onde as
solicitações do usuário são roteadas para um gerenciador (o controlador) que é
responsável por invocar as regras de negócio (o modelo) e, após o processamento
dos dados, enviá-los para a interface do usuário (a exibição).

Esse padrão promove uma clara Separação das Preocupações (SOC).

## 2. Bootstrap

No arquivo de bootstrap do sistema (ver [Criando uma aplicação](docs/pt-br/01-instanciando.md)), deve-se implementar
a inicialização de uma aplicação que use o motor `MvcEngine`:

```php
<?php

declare(strict_types=1);

use Iquety\Application\Application;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Http\Adapter\HttpFactory\DiactorosHttpFactory;

ini_set('display_errors', 1);
error_reporting(E_ALL);

require dirname(__DIR__) . '/vendor/autoload.php';

$app = Application::instance();

$app->bootEngine(new MvcEngine());

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
nosso módulo de `MeuModuloMvc`:

```php
class MeuModuloMvc extends MvcModule
{
    public function bootDependencies(Container $container): void
    {
        // dependência obrigatória para Mvc
        $container->addSingleton(Session::class, MemorySession::class);

        // dependência obrigatória para Mvc
        $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());

        // dependência adicional
        $container->addFactory(MinhaInterface::class, new MinhaImplementacao());
    }

    public function bootRoutes(Router &$router): void
    {
        // mapeia o URI /usuario/editar/<qualquer valor> para MeuControlador
        // quando a requisição for do tipo GET
        $router->get('/usuario/editar/:id')->usingAction(MeuControlador::class, 'editar');
    }
}
```

## 4. Mapeamento de rotas

### 4.1. Para objetos

Como visto no método `MeuModuloMvc::bootRoutes` deve-se configurar as rotas
disponíveis na aplicação. Cada URI deve ser mapeada para um verbo (GET, POST etc),
um controlador e uma ação.

```php
public function bootRoutes(Router &$router): void
{
    $router->get('/usuario/editar/:id')->usingAction(MeuControlador::class, 'editar');
}
```

No exemplo acima, o método `editar` do controlador `MeuControlador` é mapeado para
o URI `/usuario/editar/<qualquer-valor>` quando o verbo HTTP usado for `GET`.

Outros verbos disponíveis são:

```php
$router->any('...');
$router->get('...');
$router->post('...');
$router->put('...');
$router->patch('...');
$router->delete('...');
```

> Obs: O método `any` irá disponibilizar o controlador para qualquer verbo.

### 4.2. Para callbacks

Quando não for necessário implementar um controlador, pode-se adicionar um
callback diretamente ao mapear um URI:

```php
public function bootRoutes(Router &$router): void
{
    $router->get('/usuario/editar/:id')->usingAction(function() {
        return 'olá';
    });
}
```

No exemplo acima, o callback será mapeado para o URI `/usuario/editar/<qualquer valor>`
quando o verbo HTTP usado for `GET`. O retorno do callback será usado como
resposta da aplicação.

## 5. Implementação do Controlador

A última coisa a fazer, é criar controladores para serem executados. Na configuração
do `MeuModuloMvc`, determinou-se que quando uma requisição for feita usando o verbo GET
para a rota `/usuario/editar/<qualquer valor>`, o método `editar` do controlador
`MeuControlador` será invocado para fabricar a resposta. A seguir, implementaremos
o arquivo `MeuControlador.php`:

```php
<?php

declare(strict_types=1);

namespace Acme\Meus\Controladores\Aqui;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;

class MeuControlador extends Controller
{
    // A Injeção de Dependências irá procurar por 
    // uma dependência identificada como MinhaInterface::class
    // se tiver sido registrada em MeuModuloMvc::bootDependencies
    // então será disponibilizada para o argumento $dep
    public function __construct(MinhaInterface $dep)
    {
    }

    // O parâmetro :id mapeado na rota em MeuModuloMvc 
    // capturará qualquer valor fornecido na requisição 
    // e disponibilizará no argumento $id
    //
    // O argumento $nomeQualquer receberá a Injeção de Dependências
    public function editar(Input $input, string $id, MinhaInterface $nomeQualquer): string
    {
        return 'Resposta em texto';
    }
}
```

> **Injeção de Dependências:** para mais informações sobre o assunto, veja [arquitetura hexagonal](08-arquitetura-hexagonal.md).

No mapeamento de rotas, além dos verbos `get`, `post`, `put`, `patch` e `delete`,
é possível mapear um controlador para o verbo especial `any`.

```php
// MeuModuloMvc.php

public function bootRoutes(Router &$router): void
{
    $router->any('/usuario/editar/:id')->usingAction(MeuControlador::class, 'editar');
}
```

Ao usar o verbo especial `any`, será possível filtrar o verbo desejado na
implementação do próprio controlador, usando o método `forMethod` para restringir
a requisição ao verbo adequado.

```php
// MeuControlador.php

public function editar(Input $input, string $id, MinhaInterface $nomeQualquer): string
{
    // o método editar somente continuará a execução se 
    // o verbo da requisição for POST
    $this->forMethod(HttpMethod::POST);
}
```

[◂ Motores](04-motores.md) | [Índice da documentação](indice.md) | [Motor FrontController ▸](06-motor-fc.md)
-- | -- | --
