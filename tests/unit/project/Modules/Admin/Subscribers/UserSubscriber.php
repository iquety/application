<?php

declare(strict_types=1);

namespace Modules\Admin\Subscribers;

use Iquety\Application\AppEngine\Action\Subscriber;
use Iquety\PubSub\Event\Event;

class UserSubscriber extends Subscriber
{
    /**
     * @param array<string,mixed> $eventData
     */
    public function eventFactory(string $eventLabel, array $eventData): ?Event
    {
        // if ($eventLabel === 'event-one') {
        //     return EventOne::factory($eventData);
        // }

        return null;
    }

    public function handleEvent(Event $event): void
    {
        // $file = new Filesystem(dirname(__DIR__, 2) . '/files');

        // $file->setFileContents(
        //     'subscriber-one-handle.txt',
        //     __CLASS__ . PHP_EOL .
        //     'recebeu: ' . $event::class . PHP_EOL .
        //     'em: ' . $event->ocurredOn()->format('Y-m-d H:i:s')
        // );
    }
}
