<?php

declare(strict_types=1);

namespace Iquety\Application\PubSub;

use Iquety\PubSub\Event\Event;

abstract class DomainEvent extends Event
{
    public function subscribedToEventType(): string
    {
        // Apenas eventos deste tipo serão recebidos por este assinante
        return DomainEvent::class;
    }
}
