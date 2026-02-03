# Motor FrontController

[◂ MVC engine](05-mvc-engine.md) | [Documentation index](index.md) | [Console Engine ▸](07-console-engine.md)
-- | -- | --

## 1. Introdução

Basically, the Front Controller is composed of a Web Handler (a single controller)
that receives all user requests. There is also a hierarchy of classes where each
class represents an action to be executed (command objects).

When the user makes a request `/user/edit/22`, for example, the Web Handler will
search the command hierarchy. If the User/Edit class is found, it will be used
to provide a response to the user.

The operation is very similar to MVC, however, it tends to provide an even better
and more well-defined Separation of Concerns (SOC).

## 2. Bootstrap

In the system bootstrap file (usually index.php), you must implement the
initialization of an application that uses the `FcEngine` engine as in the
example below:

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

// We register the instance of the main module
$app->bootApplication(...);

// We register the instances of one or more secondary modules
$app->bootModule(...);

$request = new DiactorosHttpFactory();

$response = $app->run($request->createRequestFromGlobals());

$app->sendResponse($response);
```

## 3. Module Implementation

Now that the application initialization is implemented, we need to provide the
instance of our module to the `bootApplication` method (if it is the main module)
or `bootModule` (if it is a secondary module). For didactic purposes, let's call
our module `MyFcModule`:

```php
class MyFcModule extends FcModule
{
    public function bootDependencies(Container $container): void
    {
        // mandatory dependency for FrontController
        $container->addSingleton(Session::class, MemorySession::class);

        // mandatory dependency for FrontController
        $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());

        // additional dependency
        $container->addFactory(MyInterface::class, new MyImplementation());
    }

    public function bootNamespaces(SourceSet &$sourceSet): void
    {
        // determines that commands will be searched for in this namespace
        $sourceSet->add(new Source('Acme\My\Commands'));
    }
}
```

## 5. Command Implementation

The last thing to do is to create commands to be executed. In the configuration
of `MyFcModule`, it was determined that when a request is made, a corresponding
command will be searched for in the namespace `Acme\My\Commands`. Next, we will
implement the `MyCommand.php` file:

```php
<?php

declare(strict_types=1);

namespace Acme\Meus\Comandos\Aqui;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\FrontController\Command\Command;
use Iquety\Http\HttpMethod;

class MyCommand extends Command
{
    // Dependency Injection will look for
    // a dependency identified as MyInterface::class
    // if it has been registered in MyMvcModule::bootDependencies
    // then it will be made available to the $dep argument
    public function __construct(MyInterface $dep)
    {
    }

    // The $anyName argument will receive Dependency Injection
    public function execute(Input $input, MyInterface $anyName): string
    {
        // The command `MyCommand` will only continue execution 
        // if the request verb is `POST`.
        $this->forMethod(HttpMethod::POST);

        return 'Text response';
    }
}
```

> **Dependency Injection:** for more information on the subject, see [hexagonal architecture](08-hexagonal-architecture.md).

Unlike the MVC engine, where a Controller can have multiple actions, a Command
always has a single action called `execute`.

Another difference is that, since there is no router, it will be necessary to
specify which HTTP verb will be enabled to execute the command. This is done in
the command implementation, using the `forMethod` method to define the
appropriate verb.

[◂ MVC engine](05-mvc-engine.md) | [Documentation index](index.md) | [Console Engine ▸](07-console-engine.md)
-- | -- | --
