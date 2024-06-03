# Motor MVC

[◂ Motores](04-motores.md) | [Índice da documentação](indice.md) | [Motor FrontController ▸](06-motor-fc.md)
-- | -- | --

## 1. Bootstrap

O MVC (sigla de Model, View e Controller) é um padrão arquitetural onde as
solicitações do usuário são roteadas para um gerenciador (o controlador) que é
responsável por invocar as regras de negócio (o modelo) e, após o processamento
dos dados, enviá-los para a interface do usuário (a exibição).

Esse padrão promove uma clara Separação das Preocupações (SOC).

Para configurar rotas para o motor MVC, é preciso implementar um bootstrap
do tipo `MvcBootstrap`:

```php
// CustomMvcBootstrap.php

class CustomMvcBootstrap extends MvcBootstrap
{
    public function bootDependencies(Container $container): void
    {
        $container->addSingleton(Session::class, MemorySession::class);

        $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());
    }

    public function bootRoutes(Router &$router): void
    {
        $router->get('/usuario/editar/:id')->usingAction(UserController::class, 'edit');
    }
}
```

```php
// index.php

$app = Application::instance();

$app->bootEngine(new MvcEngine());

$app->bootApplication(new CustomMvcBootstrap());

$response = $app->run();

$app->sendResponse($response);
```

## 2. Adicionando dependências

No método `bootDependencies` deve-se configurar as dependências que estarão disponíveis para
a execução dos controladores.

```php
public function bootDependencies(Container $container): void
{
    $container->addSingleton(Session::class, MemorySession::class);

    $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());
}
```

Tudo o que for declarado aqui estará disponível para a Inversão de Controle, e
poderá ser invocado como argumento nos métodos dos controladores.

```php
// UserController.php

class UserController extends Controller
{
    public function edit(Input $input, int $id, HttpFactory $factory): ResponseInterface
    {
        // A Inversão de Controle injetou o HttpFactory aqui como argumento
    }
}
```

## 3. Mapeando rotinas

### 3.1. Controladores

No método `bootRoutes` deve-se configurar as rotas disponíveis na aplicação.
Cada URI deve ser mapeada para um verbo, um controlador e uma ação.

```php
public function bootRoutes(Router &$router): void
{
    $router->get('/usuario/editar/:id')->usingAction(UserController::class, 'edit');
}
```

No exemplo acima, o método `edit` do controlador `UserController` é mapeado para
o URI `/usuario/editar/<algum-número>` quando o verbo HTTP usado for `GET`.

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

### 3.2. Callbacks

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

No exemplo acima, o callback será mapeado para o URI `/usuario/editar/<algum-número>`
quando o verbo HTTP usado for `GET`. O retorno do callback será usado como
resposta da aplicação.

### 3.3. Anatomia de um controlador

A partir do roteador, é possível definir os verbos `get`, `post`, `put`, `patch`
e `delete`, de forma que o controlador mapeado só funcionará para o verbo específico.

Caso uma rota seja definida com o verbo especial `any`, será possível filtrar
o verbo desejado na implementação do comando, usando o método `forMethod` para
definir o verbo adequado.

```php
public function execute(Input $input, int $id, HttpFactory $factory): ResponseInterface
{
    $this->forMethod(HttpMethod::POST);

    // A Inversão de Controle injetou o HttpFactory aqui como argumento
}
```

[◂ Motores](04-motores.md) | [Índice da documentação](indice.md) | [Motor FrontController ▸](06-motor-fc.md)
-- | -- | --
