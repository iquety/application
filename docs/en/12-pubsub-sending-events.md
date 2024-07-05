# PubSub: sending events

[◂ Publish/Subscribe pattern](11-pubsub.md) | [Documentation index](index.md) | [PubSub: receiving events ▸](13-pubsub-receiving-events.md)
-- | -- | --

## 1. What is an Event

An event is the encapsulation of information that represents an action that
occurred at a specific moment in time. Events should always be named in the past,
as they are something that has already happened (e.g.: UserRegistered, PasswordChanged,
etc.). The "consequences" of an event are determined by the subscriber (Subscriber),
as will be explained in [PubSub: receiving events](13-pubsub-receiving-events.md).

## 2. How to implement an Event

A new event must fulfill the `Iquety\Application\PubSub\DomainEvent` contract.

The minimum implementation must include the `__constructor` and `label` methods:

### 2.1. Constructor

All events must receive their values ​​only through the constructor. It should not
be possible to change them after instantiation, in order to guarantee their
immutability.

> **Important:** Date values ​​must implement `DateTimeImmutable`!

```php
public function __construct(
    private string $name,
    private string $cpf,
    private DateTimeImmutable $schedule
) {
}
```

### 2.2. The label method

This method must return a **unique textual identification**, which names the
event in a clear and objective way. It must be a declarative name and easily
recognizable by humans.

Good examples of identification are 'user_registered' or 'user.registered'.

Bad examples are 'registered', '12345' or 'abst345sd'.

```php
public function label(): string
{
    return 'user.registered';
}
```

### 2.3. Getters

Getters can be implemented, as long as they do not change the current state of
the event and only function as data accessors.

```php
class UserRegistered extends Iquety\Application\PubSub\DomainEvent
{
    public function __construct(
        private string $name,
        private string $cpf,
        private DateTimeImmutable $schedule
    ) {
    }

    public function label(): string
    {
        return 'user.registered';
    }

    public function cpf(): string
    {
        return $this->cpf;
    }

    public function name(): string
    {
        return $this->name;
    }
}
```

### 2.4. Inherited methods in the event

The abstract class `Iquety\Application\PubSub\DomainEvent` provides four
specific methods:

#### 2.4.1. The "factory" method

This method receives an associative array containing the event data (`$values`).
Based on these values, the `factory` should manufacture the event and return it
appropriately upon return. If an additional value called `occurredOn` is provided
with an instance of `DateTimeImmutable`, the date will be applied to the event
that will be created.

> **Important:** The return value must always be an event of the same type, and
the impossibility of creating a new event must trigger an exception.

This method can be overridden to favor event backward compatibility. If the
implementation of the values ​​of an existing event needs to change, whether due
to an evolution in the system or a necessary correction, this method must guarantee
the maximum possible backward compatibility with data implemented in previous
versions. This is necessary to ensure that modules or subsystems that have not
yet updated can continue sending events, even if incomplete.

```php
/** @param array<string,mixed> $values */
public static function factory(array $values): Event
{
    // in the previous version 'cpf' was called 'document'
    if (isset($values['document']) === true) {
        $values['cpf'] = $values['document'];
    }

    return parent::factory($values);
}
```

#### 2.4.2. The "occurredOn" method

This method returns an instance of `DateTimeImmutable`, containing the value for
the current date and time, representing the moment when the event happened.

```php
public function occurredOn(): DateTimeImmutable;
```

#### 2.4.3. The "sameEventAs" method

This method compares two instances to determine whether they are the same event.

```php
/** @param UserRegistered $other */
public function sameEventAs(Event $other): bool;
```

#### 2.4.4. The "toArray" method

This method returns an associative array containing the event values ​​in simple
primitive types: string, int, float and bool. In addition to the arguments passed
in the constructor, this method will return an additional value called 'occurredOn'
with the time of the event's occurrence.

```php
public function toArray(): array;
```

### 2.5. Example

Below is an example implementation for the "UserRegistered" event:

```php
declare(strict_types=1);

namespace Foo\User\Events;

use DateTimeImmutable;
use Iquety\PubSub\Event\Event;

class UserRegistered implements Event
{
    public function __construct(
        private string $name,
        private string $cpf,
        private DateTimeImmutable $ocurredOn
    ) {
    }

    public function label(): string
    {
        return 'user.registered';
    }

    /** @param array<string,mixed> $values */
    public static function factory(array $values): Event
    {
        // in the previous version 'cpf' was called 'document'
        if (isset($values['document']) === true) {
            $values['cpf'] = $values['document'];
        }
        
        return new self(
            $values['name'],
            $values['cpf'],
            new DateTimeImmutable($values['ocurredOn'])
        );
    }

    public function cpf(): string
    {
        return $this->cpf;
    }

    public function name(): string
    {
        return $this->name;
    }
}
```

## 3. How to publish an event

Both implementations, both [Command (FcEngine)](06-fc-engine.md) and
[Controller (MvcEngine)](05-mvc-engine.md) have the `publish` method to send
events to the [registered subscribers](13-pubsub-receiving-events.md).

```php
// UserController.php

class UserController extends Controller
{
    public function edit(): ResponseInterface
    {
        $this->publish('receiver-channel', new UserRegistered(...));
    }
}
```

In the example above, the `edit` method of the `UserController` controller
publishes the `UserRegistered` event on the `'receiver-channel'` channel.

The moment the event is published, all subscribers will be consulted.
Those who are able to receive the event will use it.

[◂ Publish/Subscribe pattern](11-pubsub.md) | [Documentation index](index.md) | [PubSub: receiving events ▸](13-pubsub-receiving-events.md)
-- | -- | --
