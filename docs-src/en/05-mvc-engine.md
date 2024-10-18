# MVC engine

--page-nav--

## 1. Introduction

MVC (acronym for Model, View, and Controller) is an architectural pattern where
user requests are routed to a manager (the controller) that is responsible for
invoking business rules (the model) and, after processing the data, sending it
to the user interface (the view).

This pattern promotes a clear Separation of Concerns (SOC).

## 2. Bootstrap

In the system's bootstrap file (usually index.php), you must implement the
initialization of an application that uses the `MvcEngine` engine as in the
example below:

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

$app->bootApplication(...); // here we will place the module instance
$app->bootModule(...); // It can be here too, as a secondary module

$request = new DiactorosHttpFactory();

$response = $app->run($request->createRequestFromGlobals());

$app->sendResponse($response);
```

## 3. Module Implementation

Now that the application initialization is implemented, we need to provide the
instance of our module to the `bootApplication` method (if it is the main module)
or `bootModule` (if it is a secondary module). For didactic purposes, let's call
our module `MyMvcModule`:

```php
class MyMvcModule extends MvcModule
{
    public function bootDependencies(Container $container): void
    {
        // mandatory dependency for Mvc
        $container->addSingleton(Session::class, MemorySession::class);

        // mandatory dependency for Mvc
        $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());

        // additional dependency
        $container->addFactory(MyInterface::class, new MyImplementation());
    }

    public function bootRoutes(Router &$router): void
    {
        // maps the URI /user/edit/<any value> to MyController when the request
        // is of the GET type
        $router->get('/user/edit/:id')->usingAction(MyController::class, 'edit');
    }
}
```

## 4. Route mapping

### 4.1. For objects

As seen in the `MyMvcModule::bootRoutes` method, you must configure the routes
available in the application. Each URI must be mapped to a verb, a controller
and an action.

```php
public function bootRoutes(Router &$router): void
{
    $router->get('/user/edit/:id')->usingAction(MyController::class, 'edit');
}
```

In the above example, the `edit` method of the `MyController` controller is
mapped to the URI `/user/edit/<any-value>` when the HTTP verb used is `GET`.

Other available verbs are:

```php
$router->any('...');
$router->get('...');
$router->post('...');
$router->put('...');
$router->patch('...');
$router->delete('...');
```

> Note: The `any` method will make the controller available to any verb.

### 4.2. For callbacks

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

In the example above, the callback will be mapped to the URI `/user/edit/<any value>`
when the HTTP verb used is `GET`. The callback return will be used as the
application's response.

## 5. Controller Implementation

The last thing to do is to create controllers to be executed. In the configuration
of `MyMvcModule`, it was determined that when a request is made using the GET
verb for the `/user/edit/<any value>` route, the `edit` method of the
`MyController` controller will be invoked to produce the response. Next, we will implement
the `MyController.php` file:

```php
<?php

declare(strict_types=1);

namespace Acme\My\Controllers;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;

class MyController extends Controller
{
    // Dependency Injection will look for a dependency identified
    // as MyInterface::class if it has been registered in
    // MyMvcModule::bootDependencies then it will be made available
    // to the $dep argument
    public function __construct(MyInterface $dep)
    {
    }

    // The :id parameter mapped to the route in MyMvcModule
    // will capture any value provided in the request
    // and make it available in the $id argument
    //
    // The $anyName argument will receive Dependency Injection
    public function edit(Input $input, string $id, MyInterface $anyName): string
    {
        return 'Text response';
    }
}
```

> **Dependency Injection:** For more information on this topic, see
[hexagonal architecture](08-hexagonal-architecture.md).

In route mapping, in addition to the verbs `get`, `post`, `put`, `patch` and `delete`,
it is possible to map a controller to the special verb `any`.

```php
// MyMvcModule.php

public function bootRoutes(Router &$router): void
{
    $router->any('/user/edit/:id')->usingAction(MyController::class, 'edit');
}
```

By using the special verb `any`, it will be possible to filter the desired verb
in the implementation of the controller itself, using the `forMethod` method to
restrict the request to the appropriate verb.

```php
// MyController.php

public function edit(Input $input, string $id, MyInterface $anyName): string
{
    // the edit method will only continue execution
    // if the request verb is POST
    $this->forMethod(HttpMethod::POST);
}
```

--page-nav--
