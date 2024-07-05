# Publish/Subscribe pattern

[◂ Existing dependencies](10-existing-dependencies.md) | [Documentation index](index.md) | [PubSub: sending events ▸](12-pubsub-sending-events.md)
-- | -- | --

## 1. Event-driven architecture

The first thing to do when implementing an event-driven architecture is to have
a means of uninterruptedly checking the application, waiting for some event to
happen. When an event is identified, it must be dispatched to the routines or
system modules that are prepared to deal with that type of event.

## 2. The Publish/Subscribe approach

There are several ways to implement an event-driven architecture (APIs Rest,
Observers, Messaging Tools, etc).

This library implements a specific forwarding architectural pattern of events
called `Publish/Subscribe` or `PubSub` (Publish/Subscribe in Portuguese).

## 3. Adding publishers

The application is prepared to use the `Publish/Subscribe` pattern, making use
of the [iquety/pubsub](https://github.com/iquety/pubsub) library.

To publish events, you must initialize one or more publishers. In the following
example, `SimpleEventPublisher` will be used:

```php
// index.php

$app = Application::instance();

// initializes an event publisher
$app->bootEventPublisher(SimpleEventPublisher::instance());

$app->bootEngine(new MvcEngine());

$app->bootApplication(new MainMvcBootstrap());

$response = $app->run();
```

Currently, the [available publishers](https://github.com/iquety/pubsub/tree/main/src/Publisher) are:

| Publisher           | Description |
|:--                   | :--       |
| PhpEventPublisher    | For use with a [message broker](https://github.com/iquety/pubsub/blob/main/docs/en/03-implementing-in-broker.md) (*a routine that executes in a asynchronous on the server side and waits for events via socket*). It provides real separation between the modules that communicate with the system. |
| SimpleEventPublisher | The simplest way, which uses an ["Observer"](https://github.com/iquety/pubsub/blob/main/docs/en/02-implementing-in-bootstrap.md) that waits for events in the application itself. |

[◂ Existing dependencies](10-existing-dependencies.md) | [Documentation index](index.md) | [PubSub: sending events ▸](12-pubsub-sending-events.md)
-- | -- | --
