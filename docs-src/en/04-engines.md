# Engines

--page-nav--

In this library, the object that handles user requests is named **"engine"**.
Each engine implements a different architectural pattern. The most famous of
these is known as MVC, but others can be used.

This library offers the possibility of using different engines to
each need and can be executed at the same time. For example, it is possible
use MVC for one range of URIs and FrontController for another range.

```php
<?php

declare(strict_types=1);

use Iquety\Application\Application;
use Iquety\Application\IoEngine\FrontController\FcEngine;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Http\Adapter\HttpFactory\DiactorosHttpFactory;

ini_set('display_errors', 1);
error_reporting(E_ALL);

require dirname(__DIR__) . '/vendor/autoload.php';

$app = Application::instance();

$app->bootEngine(new MvcEngine());
$app->bootEngine(new FcEngine());

$app->bootApplication(...); // here we will place the module instance
$app->bootModule(...); // It can be here too, as a secondary module

$request = new DiactorosHttpFactory();

$response = $app->run($request->createRequestFromGlobals());

$app->sendResponse($response);
```

The **Module** will contain the necessary implementation for the desired engine
to find the Controllers (Mvc), the Commands (FrontController) or the Routines (Console).

--page-nav--
