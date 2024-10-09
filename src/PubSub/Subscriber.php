<?php

declare(strict_types=1);

namespace Iquety\Application\PubSub;

use DateTimeZone;
use Iquety\Application\Application;
use Iquety\PubSub\Event\Event;
use Iquety\PubSub\Subscriber\EventSubscriber;

abstract class Subscriber implements EventSubscriber
{
    /**
     * @param array<string,mixed> $eventData
     */
    abstract public function eventFactory(string $eventLabel, array $eventData): ?Event;

    abstract public function handleEvent(Event $event): void;

    public function receiveInTimezone(): DateTimeZone
    {
        return Application::instance()->timeZone();
    }
}
