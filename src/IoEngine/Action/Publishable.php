<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Action;

use Iquety\Application\Application;
use Iquety\PubSub\Event\Event;

trait Publishable
{
    protected function publish(string $channel, Event $event): void
    {
        Application::instance()->eventPublisherSet()->publish($channel, $event);
    }
}
