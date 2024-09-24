<?php

declare(strict_types=1);

namespace Tests;

use Iquety\Application\AppEngine\PublisherSet;
use Iquety\Application\Application;
use Iquety\PubSub\Publisher\SimpleEventPublisher;
use RuntimeException;
use Tests\PubSub\FakeSubscriber;

class ApplicationPubSubTest extends ApplicationCase
{
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function bootEventPublisher(): void
    {
        $instance = Application::instance();

        $instance->bootEventPublisher(SimpleEventPublisher::instance());

        $this->assertInstanceOf(PublisherSet::class, $instance->eventPublishers());
        $this->assertSame([
            SimpleEventPublisher::instance()
        ], $instance->eventPublishers()->toArray());
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function eventPublishers(): void
    {
        $instance = Application::instance();

        $this->assertInstanceOf(PublisherSet::class, $instance->eventPublishers());
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function addSubscriberException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No event publishers specified for the application');

        $instance = Application::instance();

        $instance->addSubscriber('meu-canal', FakeSubscriber::class);
    }
}
