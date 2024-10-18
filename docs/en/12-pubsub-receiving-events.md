# PubSub: recebendo eventos

[◂ PubSub: enviando eventos](11-pubsub-sending-events.md) | [Documentation index](index.md) | [Architecture ▸](98-architecture.md)
-- | -- | --

## 1. What is a Subscriber

A Subscriber is responsible for handling the events that occur. It must contain
the routine responsible for interpreting an event and knowing what to do when an
event of that type happens.

A new Subscriber must extend the abstract class `Iquety\Application\PubSub\Subscriber`,
which requires three specific methods.

## 2. The "eventFactory" method

This method receives an identification string (`$eventLabel`) and an associative
array containing the event data (`$eventData`). Based on this information,
"`eventFactory`" must manufacture the correct event and return it appropriately
upon return. If it is not possible to manufacture a suitable event, null must be
returned:

```php
/** @param array<string,mixed> $eventData */
public function eventFactory(string $eventLabel, array $eventData): ?Event
{
    // hmm... let's make UserRegistered
    if ($eventLabel === 'user-registered') { 
        return UserRegistered::factory($eventData);
    }

    return null;
}
```

## 3. The "handleEvent" method

This method receives an instance of an event and must invoke the appropriate
business rule for it. For example, if it is a registration event, you can invoke
some repository or service that performs the appropriate registration.

```php
public function handleEvent(Event $event): void
{
    if ($event instanceof UserRegistered) {
        // ...
        // routine that creates a new user in the database

        return;
    }

    if ($event instanceof UserEmailChanged) {
        // ...
        // routine that updates the email of an existing user in the database
    }
}
```

## 4. The "subscribedToEventType" method

This method must return the type of event that the Subscriber is capable of handling.
Only events of this type will be received in the `handleEvent` method.

```php
public function subscribedToEventType(): string
{
    // Only events of this type will be received by this subscriber
    return UserEvent::class;
}
```

> **Important:** The type of event can be determined through polymorphism. For
example, if `subscribedToEventType` returns the type `UserEvent`, only events
that implement the `UserEvent` interface will be received in the `handleEvent`
method.

## 5. Example of a Subscriber

Below, an example implementation for "`UserEventSubscriber`":

```php
declare(strict_types=1);

namespace Foo\User;

use Foo\User\Events\UserEmailChanged;
use Foo\User\Events\UserRegistered;
use Iquety\PubSub\Event\Event;
use Iquety\PubSub\Subscriber\EventSubscriber;

class UserEventSubscriber implements EventSubscriber
{
    /** @param array<string,mixed> $eventData */
    public function eventFactory(string $eventLabel, array $eventData): ?Event
    {
        if ($eventLabel === 'user-registered') {
            return UserRegistered::factory($eventData);
        }

        if ($eventLabel === 'user-email-changed') {
            return UserEmailChanged::factory($eventData);
        }

        return null;
    }

    public function handleEvent(Event $event): void
    {
        if ($event instanceof UserRegistered) {
            // Here we implement the routine that
            // creates a new user in the database

            return;
        }

        if ($event instanceof UserEmailChanged) {
            // Here we implement the routine that updates
            // the email of an existing user in the database
        }
    }

    public function subscribedToEventType(): string
    {
        // Only events of this type will be received by this subscriber
        return UserEvent::class;
    }
}
```

[◂ PubSub: enviando eventos](11-pubsub-sending-events.md) | [Documentation index](index.md) | [Architecture ▸](98-architecture.md)
-- | -- | --
