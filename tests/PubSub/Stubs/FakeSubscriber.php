<?php

declare(strict_types=1);

namespace Tests\PubSub\Stubs;

use Iquety\Application\PubSub\Subscriber;
use Iquety\PubSub\Event\Event;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class FakeSubscriber extends Subscriber
{
    /** @param array<string,mixed> $eventData */
    public function eventFactory(string $eventLabel, array $eventData): ?Event
    {
        if ($eventLabel === 'post.register.v1') {
            return FakeEventOccurred::factory($eventData);
        }

        return null;
    }

    public function handleEvent(Event $event): void
    {
        printf(
            "Event %s occurred",
            $event->label()
        );
    }
}
