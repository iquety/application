# Motor Console

--page-nav--

## 1. Bootstrap

Este motor serve para a criação de scripts de terminal.

Para configurar a localização da hierarquia de scriptss para o mecanismo Console,
é preciso implementar um inicializador do tipo `ConsoleModule`:

```php
// CustomConsoleModule.php

class CustomConsoleModule extends ConsoleModule
{
    public function bootDependencies(Container $container): void
    {
        $container->addSingleton(Session::class, MemorySession::class);

        $container->addFactory(MyInterface::class, new MyImplementation());
    }

    public function bootRoutineDirectories(RoutineSourceSet &$sourceSet): void
    {
        $sourceSet->add(new RoutineSource(__DIR__ . '/Meus/Scripts/Aqui'));
    }

    /** Devolve o nome real do script php que implementa o Console */
    public function getScriptName(): string
    {
        return 'nome do script principal';
    }

    /** Devolve o diretório real da aplicação que implementa o Console */
    public function getScriptPath(): string
    {
        return __DIR__;
    }
}
```

```php
// index.php

$app = Application::instance();

$app->bootEngine(new ConsoleEngine());

$app->bootApplication(new CustomConsoleModule());

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

    $container->addFactory(MyInterface::class, new MyImplementation());
}
```

## 3. Implementando rotinas

### 3.1. Executando Comandos

No método `bootRoutineDirectories`, deve-se configurar os namespaces liberados para
o gerenciador procurar por rotinas.

```php
public function bootRoutineDirectories(RoutineSourceSet &$sourceSet): void
{
    $sourceSet->add(new RoutineSource(__DIR__ . '/Meus/Scripts/Aqui'));
}
```

No exemplo acima, todos os scripts que estejam no diretório "__DIR__ . '/Meus/Scripts/Aqui'".

### 3.2. Anatomia de um script

Diferente do motor MVC, onde um Controlador pode possuir várias ações,
um Comando possui sempre uma única ação chamada `execute`.

```php
public function execute(Input $input, int $id, HttpFactory $factory): ResponseInterface
{
    $this->forMethod(HttpMethod::POST);

    // A Inversão de Controle injetou o HttpFactory aqui como argumento
}
```


--page-nav--
