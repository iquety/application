<?php

declare(strict_types=1);

namespace Iquety\Application\PubSub;

use Iquety\Application\Application;
use Iquety\PubSub\Event\Event;

trait Publisher
{
    protected function publish(string $channel, Event $event): void
    {
        Application::instance()->eventPublishers()->publish($channel, $event);
    }
}
