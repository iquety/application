# Motor FrontController

[◂ Motor MVC](05-motor-mvc.md) | [Índice da documentação](indice.md) | [Implementando módulos ▸](07-modulos.md)
-- | -- | --

## 1. Bootstrap

Basicamente o Front Controller é composto por um Manipulador Web (um controlador
único) que recebe todas as solicitações do usuário. Também existe uma hierarquia
de classes onde cada uma delas representa uma ação a ser executada (objetos de comando).

Quando o usuário faz uma solicitação `/usuario/editar/22`, por exemplo, o Manipulador Web
irá procurar na hierarquia de comandos. Se a classe Usuario/Editar for encontrada,
ela será usada para prover uma resposta para o usuário.

O funcionamento é muito semelhante ao MVC, porém, tende a prover uma Separação de
Preocupações (SOC) ainda melhor e mais bem definida.

Para configurar a localização da hierarquia de comandos para o mecanismo FrontController,
é preciso implementar um bootstrap do tipo `FcBootstrap`:

```php
// CustomFcBootstrap.php

class CustomFcBootstrap extends FcBootstrap
{
    public function bootDependencies(Container $container): void
    {
        $container->addSingleton(Session::class, MemorySession::class);

        $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());
    }

    public function bootNamespaces(SourceSet &$sourceSet): void
    {
        $sourceSet->add(new Source('MyCommands\SubDirectory'));
    }
}
```

```php
// index.php

$app = Application::instance();

$app->bootEngine(new FcEngine());

$app->bootApplication(new CustomFcBootstrap());

$response = $app->run();

$app->sendResponse($response);
```

## 2. Adicionando dependências

No método `bootDependencies` deve-se configurar as dependências que estarão
disponíveis para a execução dos controladores.

```php
public function bootDependencies(Container $container): void
{
    $container->addSingleton(Session::class, MemorySession::class);

    $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());
}
```

Tudo o que for declarado aqui estará disponível para a Inversão de Controle, e
poderá ser invocado como argumento no método `execute` dos comandos.

```php
// UserCommand.php

class UserCommand extends Command
{
    public function execute(Input $input, int $id, HttpFactory $factory): ResponseInterface
    {
        // A Inversão de Controle injetou o HttpFactory aqui como argumento
    }
}
```

## 3. Implementando rotinas

### 3.1. Executando Comandos

No método `bootNamespaces`, deve-se configurar os namespaces liberados para
o Manipulador Web procurar por comandos.

```php
public function bootNamespaces(SourceSet &$sourceSet): void
{
    $sourceSet->add(new Source('MyCommands\SubDirectory'));
}
```

No exemplo acima, todos os comandos cujo namespace comece com
'MyCommands\SubDirectory' serão considerados aptos para ser executados como
comandos do FrontController.

### 3.2. Anatomia de um comando

Diferente do motor MVC, onde um Controlador pode possuir várias ações,
um Comando possui sempre uma única ação chamada `execute`.

```php
public function execute(Input $input, int $id, HttpFactory $factory): ResponseInterface
{
    $this->forMethod(HttpMethod::POST);

    // A Inversão de Controle injetou o HttpFactory aqui como argumento
}
```

Outra diferença é que, como não existe um roteador, será necessário especificar
qual verbo HTTP estará habilitado a executar o comando. Isso é feito na implementação
do comando, usando o método `forMethod` para definir o verbo adequado.

[◂ Motor MVC](05-motor-mvc.md) | [Índice da documentação](indice.md) | [Implementando módulos ▸](07-modulos.md)
-- | -- | --
