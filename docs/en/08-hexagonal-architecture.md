# Hexagonal Architecture

[◂ Console Engine](07-console-engine.md) | [Documentation index](index.md) | [Dependências existentes ▸](09-dependencies.md)
-- | -- | --

## 1. Introduction

Every programmer should have as their main objective the production of decoupled
code whenever possible. In fact, one of the most important principles of Object
Orientation is "Program for interfaces and not for implementations" and it attacks
precisely the problem of coupling.

This principle can be followed by using the [Container](https://www.php-fig.org/psr/psr-11/)
pattern to register dependencies. This way, they will be available throughout
the system.

When registering a dependency, it is good practice to use the full interface name
as the identifier:

```php

// registers a factory identified by MyInterface::class
Application::instance()
    ->addFactory(MyInterface::class, fn() => new MyImplementation());

// manufactures an instance of MyImplementation
$instance = Application::instance()->make(MyInterface::class);
```

Using factories to obtain dependencies will force the programmer to always
"think his code" from the interface and not from the implementation it provides.

## 2. Registering in Bootstrap

The ideal place to register factories is in the [application bootstrap](01-instantiating.md),
which is usually found in the `index.php` file:

```php
<?php
// index.php

declare(strict_types=1);

use Iquety\Application\Application;
use Iquety\Application\IoEngine\FrontController\FcEngine;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Http\Adapter\HttpFactory\DiactorosHttpFactory;

ini_set('display_errors', 1);
error_reporting(E_ALL);

require dirname(__DIR__) . '/vendor/autoload.php';

$app = Application::instance();

// registers a normal factory
$app->container()->addFactory('identification', 'implementation');

// registers a singleton factory
$app->container()->addSingleton('identification', 'implementation');

// runs the application, and the components can manufacture the dependencies
$response = $app->run(...);
```

## 3. Registering in modules

When the application is implemented to use modules, it may be interesting to
register specific factories for the module we want. This can be done by
registering them in the `bootDependencies` method of the module implementation:

```php
class MyMvcModule extends MvcModule
{
    public function bootDependencies(Container $container): void
    {
        // register the dependency as a Singleton
        $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());
    }

    ...
}
```

## 4. Using Inversion of Control

Both the [MVC](05-motor-mvc.md) and [FrontController](06-motor-fc.md) engines
have `Inversion of Control` in their actions (in Controllers or Commands, respectively).

This means that when adding an argument, in any method of a Controller or Command,
whose type corresponds to an interface that has been registered as an identifier
in a factory, this argument will automatically receive the manufactured dependency
as a value:

```php
class MyController extends Controller
{
    public function edit(HttpFactory $factory): ResponseInterface
    {
        // If HttpFactory::class was used as a factory identifier,
        // the $factory argument will automatically have the
        // appropriately manufactured dependency
    }
}
```

## 5. Manually building

In addition to enabling `Inversion of Control`, the [MVC](05-motor-mvc.md)
and [FrontController](06-motor-fc.md) engines allow for the manual building of
dependencies. This can be done through the `make` method, present in
Controllers and Commands, respectively:

```php
class MyController extends Controller
{
    public function edit(): ResponseInterface
    {
        $factory = $this->make(HttpFactory::class);

        // If HttpFactory::class was used as a factory identifier,
        // the $factory variable will receive the manufactured dependency
    }
}
```

[◂ Console Engine](07-console-engine.md) | [Documentation index](index.md) | [Dependências existentes ▸](09-dependencies.md)
-- | -- | --
