<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Support;

use DateTimeImmutable;
use Iquety\Application\Domain\DomainEvent;

class PostRegistered extends DomainEvent
{
    public function __construct(
        private string $title,
        private string $description,
        private DateTimeImmutable $schedule
    ) {
    }

    public function label(): string
    {
        return '';
    }
}
