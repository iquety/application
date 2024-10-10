<?php

declare(strict_types=1);

namespace Tests\PubSub;

use DateTimeImmutable;
use Iquety\Application\PubSub\DomainEvent;
use Tests\PubSub\Stubs\FakeEventOccurred;
use Tests\TestCase;

class DomainEventTest extends TestCase
{
    /** @test */
    public function subscribedToEventType(): void
    {
        $event = new FakeEventOccurred('title', 'description', new DateTimeImmutable('now'));

        $this->assertSame(DomainEvent::class, $event->subscribedToEventType());

        $this->assertSame('post.register.v1', $event->label());
        $this->assertSame('title', $event->title());
        $this->assertSame('description', $event->description());
        $this->assertInstanceOf(DateTimeImmutable::class, $event->schedule());
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function factory(): void
    {
        /** @var FakeEventOccurred $event */
        $event = FakeEventOccurred::factory([
            'title'       => 'title',
            'description' => 'description',
            'schedule'    => new DateTimeImmutable('now')
        ]);

        $this->assertSame('post.register.v1', $event->label());
        $this->assertSame('title', $event->title());
        $this->assertSame('description', $event->description());
        $this->assertInstanceOf(DateTimeImmutable::class, $event->schedule());
    }
}
