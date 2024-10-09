<?php

declare(strict_types=1);

namespace Tests\PubSub;

use DateTimeImmutable;
use DateTimeZone;
use Tests\PubSub\Stubs\FakeEventOccurred;
use Tests\PubSub\Stubs\FakeSubscriber;
use Tests\TestCase;

class SubscriberTest extends TestCase
{
    /** @test */
    public function receiveInTimezone(): void
    {
        $subscriber = new FakeSubscriber();

        $this->assertInstanceOf(DateTimeZone::class, $subscriber->receiveInTimezone());
    }

    /** @test */
    public function eventFactory(): void
    {
        $subscriber = new FakeSubscriber();

        /** @var FakeEventOccurred $event */
        $event = $subscriber->eventFactory('post.register.v1', [
            'title'       => 'title',
            'description' => 'description',
            'schedule'    => new DateTimeImmutable('now')
        ]);

        $this->assertSame('post.register.v1', $event->label());
        $this->assertSame('title', $event->title());
        $this->assertSame('description', $event->description());
        $this->assertInstanceOf(DateTimeImmutable::class, $event->schedule());
    }

    /** @test */
    public function handleEvent(): void
    {
        $subscriber = new FakeSubscriber();

        /** @var FakeEventOccurred $event */
        $event = $subscriber->eventFactory('post.register.v1', [
            'title'       => 'title',
            'description' => 'description',
            'schedule'    => new DateTimeImmutable('now')
        ]);

        ob_start();

        $subscriber->handleEvent($event);

        $this->assertSame(
            sprintf("Event %s occurred", $event->label()),
            (string)ob_get_clean()
        );
    }
}
