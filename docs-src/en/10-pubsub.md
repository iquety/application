# Publish/Subscribe Pattern

--page-nav--

## 1. Event-driven architecture

The first thing to do when implementing an event-driven architecture is to have a means of continuously checking the application, waiting for an event to occur. When an event is identified, it must be dispatched to the routines or modules of the system that are prepared to handle that type of event.

## 2. The Publish/Subscribe approach

There are several ways to implement an event-driven architecture (Rest APIs, Observers, Messaging Tools, etc.).

This library implements a specific architectural pattern for forwarding events called `Publish/Subscribe` or `PubSub`.

## 3. Adding publishers

The application is prepared to use the `Publish/Subscribe` pattern by using
the [iquety/pubsub](https://github.com/iquety/pubsub) library.

To publish events, you need to initialize one or more publishers. In the
following example, the `SimpleEventPublisher` will be used:

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

// initializes an event publisher
$app->bootEventPublisher(SimpleEventPublisher::instance());

$app->bootEngine(...); // engine 1

$app->bootApplication(...); // main module

$response = $app->run(...); // user request

$app->sendResponse($response);
```

Atualmente, os [publicadores disponíveis](https://github.com/iquety/pubsub/tree/main/src/Publisher) são:

| Publisher           | Description |
|:--                   | :--       |
| PhpEventPublisher    | For use with a [message broker](https://github.com/iquety/pubsub/blob/main/docs/en/03-implementing-in-broker.md) (*a routine that executes in a asynchronous on the server side and waits for events via socket*). It provides real separation between the modules that communicate with the system. |
| SimpleEventPublisher | The simplest way, which uses an ["Observer"](https://github.com/iquety/pubsub/blob/main/docs/en/02-implementing-in-bootstrap.md) that waits for events in the application itself. |

--page-nav--
