# Creating an application

[◂ Documentation index](index.md) | [Execution mode ▸](02-execution-mode.md)
-- | --

Every web application must have a starting point, that is, a file that will be invoked every time a request is made to the web server. This file is usually `index.php` and is located in the root directory (docroot) configured on the server. Technically, we can say that this file is the `bootstrap` of our application.

It is in this file that we must initialize the `Iquety Application` library and configure it according to the needs of our web application.

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

$app->runIn(Environment::PRODUCTION);

$app->useTimezone(new DateTimeZone('America/Vancouver'));

$app->bootEngine(...); // engine 1
$app->bootEngine(...); // engine 2

$app->bootApplication(...); // main module

$app->bootModule(...); // secondary module 1
$app->bootModule(...); // secondary module 2

$request = new DiactorosHttpFactory();

$response = $app->run($request->createRequestFromGlobals());

$app->sendResponse($response);
```

[◂ Documentation index](index.md) | [Execution mode ▸](02-execution-mode.md)
-- | --
