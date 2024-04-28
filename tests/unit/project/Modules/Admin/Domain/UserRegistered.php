<?php

declare(strict_types=1);

namespace Modules\Admin\Domain;

use Iquety\Application\Domain\DomainEvent;
use Iquety\PubSub\Event\Event;

class UserRegistered extends DomainEvent
{
    public function __construct(
        private string $username,
        private int $age,
        private string $cpf
    ) {
    }

    public function label(): string
    {
        return '';
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [];
    }
}
