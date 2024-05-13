<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Action;

use Iquety\Application\Application;
use Iquety\PubSub\Event\Event;

trait Publisher
{
    // public function addSubscriber(string $channel, string $subscriberIdentifier): void
    // {
    //     Application::instance()->eventPublisher()->subscribe($channel, $subscriberIdentifier);
    // }

    public function publish($channel, Event $event): void
    {
        Application::instance()->eventPublisher()->publish($channel, $event);
    }
}
