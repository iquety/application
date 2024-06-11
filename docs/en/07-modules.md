# Implementing modules

[◂ FrontController engine](06-fc-engine.md) | [Documentation index](index.md) | [Hexagonal Architecture ▸](09-hexagonal-architecture.md)
-- | -- | --

In addition to the main bootstrap, defined with `bootApplication`, it is possible
to initialize additional modules using `bootModule`:

```php
$app = Application::instance();

$app->bootEngine(new MvcEngine());
$app->bootEngine(new FcEngine());

$app->bootApplication(new MainMvcBootstrap());

$app->bootModule(new ModuleOneMvcBootstrap());
$app->bootModule(new ModuleTwoMvcBootstrap());
$app->bootModule(new ModuleThreeFcBootstrap());

$response = $app->run();

$app->sendResponse($response);
```

Separating the application into modules provides a powerful way for Separation of
Concerns (SOC), as well as the use of Delimited Contexts as suggested by
Domain-Driven Design.

The bootstrap must be implemented based on an engine previously initialized with
`bootEngine`. In the case above, both `MvcEngine` and `FcEngine` were initialized,
making it possible to add modules of types `MvcBootstrap` and `FcBootstrap`.

[◂ FrontController engine](06-fc-engine.md) | [Documentation index](index.md) | [Hexagonal Architecture ▸](09-hexagonal-architecture.md)
-- | -- | --
