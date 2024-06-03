# MVC engine

[◂ Engines](04-engines.md) | [Documentation index](index.md) | [FrontController engine ▸](06-fc-engine.md)
-- | -- | --

## 1. Bootstrap

MVC (acronym for Model, View and Controller) is an architectural pattern where
user requests are routed to a manager (the controller) which is responsible for
invoking the business rules (the model) and, after processing the data, send
them to the user interface (the view).

This standard promotes a clear Separation of Concerns (SOC).

To configure routes for the MVC engine, you need to implement a bootstrap of
type `MvcBootstrap`:

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
invoked as an argument in controller methods.

```php
// UserController.php

class UserController extends Controller
{
    public function edit(Input $input, int $id, HttpFactory $factory): ResponseInterface
    {
        // Inversion of Control injected the HttpFactory here as an argument
    }
}
```

## 3. Mapping routines

### 3.1. Controllers

In the `bootRoutes` method you must configure the routes available in the application.
Each URI must be mapped to a verb, a controller, and an action.

```php
public function bootRoutes(Router &$router): void
{
    $router->get('/user/edit/:id')->usingAction(UserController::class, 'edit');
}
```

In the example above, the `edit` method of the `UserController` controller is
mapped to the URI `/user/edit/<some-number>` when the HTTP verb used is `GET`.

Other available verbs are:

```php
$router->any('...');
$router->get('...');
$router->post('...');
$router->put('...');
$router->patch('...');
$router->delete('...');
```

> Note: The `any` method will make the controller available for any verb.

### 3.2. Callbacks

When it is not necessary to implement a controller, you can add a callback
directly when mapping a URI:

```php
public function bootRoutes(Router &$router): void
{
    $router->get('/user/edit/:id')->usingAction(function() {
        return 'hello';
    });
}
```

In the example above, the callback will be mapped to the URI `/user/edit/<some-number>`
when the HTTP verb used is `GET`. The callback return will be used as application
response.

### 3.3. Anatomy of a Controller

From the router, it is possible to define the verbs `get`, `post`, `put`, `patch`
and `delete`, so that the mapped controller will only work for the specific verb.

If a route is defined with the special verb `any`, it will be possible to filter
the desired verb in the command implementation, using the `forMethod` method to
define the appropriate verb.

```php
public function execute(Input $input, int $id, HttpFactory $factory): ResponseInterface
{
    $this->forMethod(HttpMethod::POST);

    // Inversion of Control injected the HttpFactory here as an argument
}
```

[◂ Engines](04-engines.md) | [Documentation index](index.md) | [FrontController engine ▸](06-fc-engine.md)
-- | -- | --
