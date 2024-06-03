# FrontController engine

[◂ MVC engine](05-mvc-engine.md) | [Documentation index](index.md) | [Implementing modules ▸](07-modules.md)
-- | -- | --

## 1. Bootstrap

Basically, the Front Controller is composed of a Web Handler (a single controller)
that receives all user requests. There is also a hierarchy of classes where each
of them represents an action to be performed (command objects).

When the user makes a request `/usuario/editar/22`, for example, the Web Handler
will search the command hierarchy. If the User/Edit class is found, it will be
used to provide a response to the user.

The operation is very similar to MVC, however, it tends to provide an even better
and more well-defined Separation of Concerns (SOC).

To configure the location of the command hierarchy for the FrontController engine,
you need to implement a bootstrap of type `FcBootstrap`:

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

## 2. Adding dependencies

In the `bootDependencies` method you must configure the dependencies that will
be available for the execution of the controllers.

```php
public function bootDependencies(Container $container): void
{
    $container->addSingleton(Session::class, MemorySession::class);

    $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());
}
```

Everything declared here will be available for Inversion of Control, and can be
invoked as an argument in the `execute` method of the commands.

```php
// UserCommand.php

class UserCommand extends Command
{
    public function execute(Input $input, int $id, HttpFactory $factory): ResponseInterface
    {
        // Inversion of Control injected the HttpFactory here as an argument
    }
}
```

## 3. Implementing routines

### 3.1. Executing Commands

In the `bootNamespaces` method, you must configure the namespaces released for
the Web Handler to search for commands.

```php
public function bootNamespaces(SourceSet &$sourceSet): void
{
    $sourceSet->add(new Source('MyCommands\SubDirectory'));
}
```

In the example above, all commands whose namespace begins with 'MyCommands\SubDirectory'
will be considered capable of being executed as FrontController commands.

### 3.2. Anatomy of a command

Unlike the MVC engine, where a Controller can have several actions, a Command
always has a single action called `execute`.

```php
public function execute(Input $input, int $id, HttpFactory $factory): ResponseInterface
{
    $this->forMethod(HttpMethod::POST);

    // Inversion of Control injected the HttpFactory here as an argument
}
```

Another difference is that, as there is no router, it will be necessary to
specify which HTTP verb will be able to execute the command. This is done in the
command implementation, using the `forMethod` method to define the appropriate verb.

[◂ MVC engine](05-mvc-engine.md) | [Documentation index](index.md) | [Implementing modules ▸](07-modules.md)
-- | -- | --
