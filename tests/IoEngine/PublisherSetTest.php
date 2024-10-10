<?php

declare(strict_types=1);

namespace Tests\IoEngine;

use DateTimeImmutable;
use InvalidArgumentException;
use Iquety\Application\IoEngine\PublisherSet;
use Iquety\PubSub\Publisher\PhpEventPublisher;
use Iquety\PubSub\Publisher\SimpleEventPublisher;
use RuntimeException;
use Tests\PubSub\Stubs\FakeEventOccurred;
use Tests\PubSub\Stubs\FakeSubscriber;
use Tests\TestCase;

class PublisherSetTest extends TestCase
{
    /** @test */
    public function hasPublishers(): void
    {
        $publisherSet = new PublisherSet();

        $publisherSet->add(new SimpleEventPublisher());

        $this->assertTrue($publisherSet->hasPublishers());
    }

    /** @test */
    public function duplicatedPublisher(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Event Publisher %s has already been registered',
            SimpleEventPublisher::class
        ));

        $publisherSet = new PublisherSet();

        $publisherSet->add(new SimpleEventPublisher());
        $publisherSet->add(new SimpleEventPublisher());
    }

    /** @test */
    public function publishersAsArray(): void
    {
        $publisherSet = new PublisherSet();

        $publisherSet->add(new SimpleEventPublisher());
        $publisherSet->add(new PhpEventPublisher());

        $this->assertCount(2, $publisherSet->toArray());
        $this->assertInstanceOf(
            SimpleEventPublisher::class,
            $publisherSet->toArray()[SimpleEventPublisher::class]
        );
        $this->assertInstanceOf(
            PhpEventPublisher::class,
            $publisherSet->toArray()[PhpEventPublisher::class]
        );
    }

    /** @test */
    public function publishWithoutPublisher(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'No event publishers specified for the application',
        );

        $event = new FakeEventOccurred(
            'titulo',
            'descricao',
            new DateTimeImmutable('now')
        );

        $publisherSet = new PublisherSet();

        $publisherSet->publish('canal1', $event);
    }

    /** @test */
    public function publishWithoutSubscriber(): void
    {
        $event = new FakeEventOccurred(
            'titulo',
            'descricao',
            new DateTimeImmutable('now')
        );

        $publisherSet = new PublisherSet();

        $publisherSet->add(new SimpleEventPublisher());

        $publisherSet->publish('canal1', $event);

        $this->assertTrue(true);
    }

    /** @test */
    public function subscribeWithoutPublisher(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'No event publishers specified for the application',
        );

        $publisherSet = new PublisherSet();

        $publisherSet->subscribe('canal1', FakeSubscriber::class);
    }

    /** @test */
    public function publishEvent(): void
    {
        $event = new FakeEventOccurred(
            'titulo',
            'descricao',
            new DateTimeImmutable('now')
        );

        $publisherSet = new PublisherSet();

        $publisherSet->add(new SimpleEventPublisher());

        $publisherSet->subscribe('canal1', FakeSubscriber::class);

        ob_start();

        $publisherSet->publish('canal1', $event);

        $this->assertSame('Event post.register.v1 occurred', ob_get_clean());
    }
}
