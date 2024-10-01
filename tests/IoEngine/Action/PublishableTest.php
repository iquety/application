<?php

declare(strict_types=1);

namespace Tests\IoEngine\Action;

use DateTimeImmutable;
use Iquety\Application\Application;
use Iquety\Application\IoEngine\Action\Publishable;
use Iquety\PubSub\Publisher\SimpleEventPublisher;
use Tests\PubSub\Stubs\FakeEventOccurred;
use Tests\PubSub\Stubs\FakeSubscriber;
use Tests\TestCase;

class PublishableTest extends TestCase
{
    /** @test */
    public function publish(): void
    {
        $eventPublisher = new SimpleEventPublisher();

        $application = Application::instance();
        $application
            ->container()
            ->addFactory('dependency', 'teste');

        $application->bootEventPublisher($eventPublisher);

        $application->addSubscriber(
            'any-channel',
            FakeSubscriber::class
        );

        $object = new class {
            use Publishable;

            public function execute(): array
            {
                $event = new FakeEventOccurred(
                    'titulo',
                    'descricao',
                    new DateTimeImmutable('now')
                );

                $this->publish('any-channel', $event);

                return $this->published();
            }
        };

        $response = $object->execute();

        $this->assertSame([
            'Event post.register.v1 occurred'
        ], $response);
    }
}
