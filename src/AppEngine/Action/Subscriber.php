<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Action;

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
    // {
    //     if ($eventLabel === 'event-one') {
    //         return EventOne::factory($eventData);
    //     }

    //     return null;
    // }

    abstract public function handleEvent(Event $event): void;
    // {
    //     $file = new Filesystem(dirname(__DIR__, 2) . '/files');

    //     $file->setFileContents(
    //         'subscriber-one-handle.txt',
    //         __CLASS__ . PHP_EOL .
    //         'recebeu: ' . $event::class . PHP_EOL .
    //         'em: ' . $event->ocurredOn()->format('Y-m-d H:i:s')
    //     );
    // }

    public function receiveInTimezone(): DateTimeZone
    {
        return Application::instance()->timezone();
    }
}
