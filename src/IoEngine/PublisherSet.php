<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine;

use InvalidArgumentException;
use Iquety\PubSub\Event\Event;
use Iquety\PubSub\Publisher\EventPublisher;
use RuntimeException;

class PublisherSet
{
    /** @var array<class-string,EventPublisher> */
    private array $publisherList = [];

    public function add(EventPublisher $publisher): void
    {
        if (isset($this->publisherList[$publisher::class]) === true) {
            throw new InvalidArgumentException(sprintf(
                'Event Publisher %s has already been registered',
                $publisher::class
            ));
        }

        $this->publisherList[$publisher::class] = $publisher;
    }

    public function hasPublishers(): bool
    {
        return $this->publisherList !== [];
    }

    public function publish(string $channel, Event $event): void
    {
        if ($this->publisherList === []) {
            throw new RuntimeException('No event publishers specified for the application');
        }

        foreach ($this->publisherList as $publisher) {
            $publisher->publish($channel, $event);
        }
    }

    public function subscribe(string $channel, string $subscriberIdentifier): void
    {
        if ($this->publisherList === []) {
            throw new RuntimeException('No event publishers specified for the application');
        }

        foreach ($this->publisherList as $publisher) {
            $publisher->subscribe($channel, $subscriberIdentifier);
        }
    }

    /** @return array<class-string,EventPublisher> */
    public function toArray(): array
    {
        return $this->publisherList;
    }
}
