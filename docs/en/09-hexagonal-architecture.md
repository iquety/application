# Hexagonal Architecture

[◂ Implementing modules](07-modules.md) | [Documentation index](index.md) | [Existing dependencies ▸](10-existing-dependencies.md)
-- | -- | --

## 1. Registering dependencies

The library was implemented to favor the use of Dependency Injection.
The ideal place to inject all dependencies is in the [application bootstrap (in the `index.php` file)](01-instantiating.md).

Good use of dependencies should follow the principle of "programming for interfaces
and not for implementations". Therefore, a good practice is to link an implementation
to the interface name and manufacture the dependency from the interface.

```php
class CustomMvcBootstrap extends MvcBootstrap
{
    public function bootDependencies(Container $container): void
    {
        // dependency registration as Singleton
        $container->addSingleton(
            HttpFactory::class,
            new DiactorosHttpFactory()
        );
    }

    ...
}
```

In the example above, the `HttpFactory` interface is used as the dependency
identifier. The `DiactorosHttpFactory` class is the implementation that will be
manufactured when `HttpFactory` is invoked.

## 2. Using Dependency Injection

Both the [MVC](05-mvc-engine.md) and [FrontController](06-fc-engine.md) engines
have Inversion of Control in Controllers and Commands, respectively. This means
that adding an argument whose type corresponds to an interface, registered in a
bootstrap, will be automatically resolved and available for use when executing
the Controller/Command:

```php
// UserController.php

class UserController extends Controller
{
    public function edit(HttpFactory $factory): ResponseInterface
    {
    }
}
```

In the example above, the implementation for `HttpFactory` will be automatically
resolved by Inversion of Control and made available as an argument to the `edit`
method.

## 3. Invoking manually

Just like Inversion of Control, the [MVC](05-mvc-engine.md) and
[FrontController](06-fc-engine.md) mechanisms allow manual invocation of
dependencies. This can be done through the `make` method, present in Controllers
and Commands, respectively:

```php
// UserController.php

class UserController extends Controller
{
    public function edit(): ResponseInterface
    {
        $factory = $this->make(HttpFactory::class);
    }
}
```

In the example above, the `make` method programmatically manufactures the
`DiactorosHttpFactory` dependency based on the `HttpFactory` interface.

[◂ Implementing modules](07-modules.md) | [Documentation index](index.md) | [Existing dependencies ▸](10-existing-dependencies.md)
-- | -- | --
