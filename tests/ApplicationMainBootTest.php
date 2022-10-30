<?php

declare(strict_types=1);

namespace Tests;

use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\Session;
use RuntimeException;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationMainBootTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function invalidSession(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "Please implement " .
                Session::class . " dependency for " .
                Application::class . "->bootApplication"
        );

        $app = Application::instance();

        $app->bootApplication(new class implements Bootstrap {
            public function bootDependencies(Application $app): void
            {
                $app->addSingleton(Session::class, fn() => (object)[]);
                $app->addSingleton(HttpFactory::class, fn() => (object)[]);
            }
        });

        $app->run();
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function invalidHttpFactory(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "Please implement " .
                HttpFactory::class . " dependency for " .
                Application::class . "->bootApplication"
        );

        $app = Application::instance();
        $app->bootApplication(new class implements Bootstrap {
            public function bootDependencies(Application $app): void
            {
                $app->addSingleton(Session::class, MemorySession::class);
                $app->addSingleton(HttpFactory::class, fn() => (object)[]);
            }
        });

        $app->run();
    }
}
