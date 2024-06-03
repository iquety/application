# Engines

[◂ The timezone](03-timezone.md) | [Documentation index](index.md) | [MVC engine ▸](05-mvc-engine.md)
-- | -- | --

In this library, we call **"engine"** the way in which requests from the
user are handled by the application. Each engine implements a standard
different architectural and the most famous of them is known as MVC.

This library offers the possibility of using different engines to
each need and can be executed at the same time. For example, it is possible
use MVC for one range of URIs and FrontController for another range.

```php
$app = Application::instance();

$app->bootEngine(new MvcEngine());
$app->bootEngine(new FcEngine());

$app->bootApplication(/* Bootstrap */);

$response = $app->run();

$app->sendResponse($response);
```

**Bootstrap** will contain the necessary implementation for the desired engine
to find the Controllers (Mvc) and Commands (FrontController).

[◂ The timezone](03-timezone.md) | [Documentation index](index.md) | [MVC engine ▸](05-mvc-engine.md)
-- | -- | --
