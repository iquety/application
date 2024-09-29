<?php

declare(strict_types=1);

namespace Tests\Application;

use InvalidArgumentException;
use Iquety\Application\Application;
use Iquety\Application\IoEngine\FrontController\FcEngine;
use Iquety\Application\IoEngine\IoEngine;
use Iquety\Application\IoEngine\Module;
use Iquety\PubSub\Publisher\EventPublisher;
use Iquety\PubSub\Publisher\SimpleEventPublisher;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ApplicationPubSubTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    public function tearDown(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function bootPublisher(): void
    {
        /** @var Module */
        $module = $this->createStub(Module::class);

        /** @var IoEngine */
        $engine = $this->createStub(IoEngine::class);
        
        /** @var EventPublisher */
        $publisherOne = $this->createStub(EventPublisher::class);

        /** @var EventPublisher */
        $publisherTwo = $this->createStub(SimpleEventPublisher::class);

        $application = Application::instance();
        $application->bootApplication($module);
        $application->bootEngine($engine);

        $application->bootEventPublisher($publisherOne);

        $application->bootEventPublisher($publisherTwo);

        $publisherList = $application->eventPublisherSet()->toArray();

        $this->assertCount(2, $publisherList);
        
        $this->assertInstanceOf(SimpleEventPublisher::class, $publisherList[$publisherTwo::class]);
    }

    /** @test */
    public function bootTwoEqualPublishers(): void
    {
        /** @var Module */
        $module = $this->createStub(Module::class);

        /** @var IoEngine */
        $engine = $this->createStub(IoEngine::class);
        
        /** @var EventPublisher */
        $publisherOne = $this->createStub(EventPublisher::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Event Publisher %s has already been registered',
            $publisherOne::class
        ));

        $application = Application::instance();
        $application->bootApplication($module);
        $application->bootEngine($engine);

        $application->bootEventPublisher($publisherOne);

        $application->bootEventPublisher($publisherOne);
    }
}
