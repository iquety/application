# Creating an application

[◂ Documentation index](index.md) | [Execution mode ▸](02-execution-mode.md)
-- | --

Every web application must have a starting point, a file that is invoked every time any request is made to the web server. Generally, this file is `index.php` and does not find any root directory (docroot) configured on the server. Technically, we can say that this file is the `bootstrap` of our application.

It is this file that we must initialize the Iquety Application library and configure the application according to the needs of our web application.

```php
// index.php

$app = Application::instance();

$app->runIn(Environment::PRODUCTION);

$app->useTimezone(new DateTimeZone('America/Vancouver'));

$app->bootEngine(/* Engine 1 */);
$app->bootEngine(/* Engine 2 */);

$app->bootApplication(/* Main Bootstrap */);

$app->bootModule(/* Bootstrap Module 1*/);
$app->bootModule(/* Bootstrap Module 2*/);

$response = $app->run();

$app->sendResponse($response);
```

[◂ Documentation index](index.md) | [Execution mode ▸](02-execution-mode.md)
-- | --
