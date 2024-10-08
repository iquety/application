<?php

declare(strict_types=1);

namespace Tests\Application;

use ArrayObject;
use Exception;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\Session\SymfonyNativeSession;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\Session;
use Iquety\Application\IoEngine\Console\ConsoleEngine;
use Iquety\Application\IoEngine\Console\ConsoleInput;
use Iquety\Application\IoEngine\Console\ConsoleModule;
use Iquety\Application\IoEngine\Console\ConsoleOutput;
use Iquety\Application\IoEngine\Console\RoutineSource;
use Iquety\Application\IoEngine\Console\RoutineSourceSet;
use Iquety\Application\IoEngine\IoEngine;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Application\IoEngine\Mvc\MvcModule;
use Iquety\Console\Terminal;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationRunnersTest extends TestCase
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
    public function runCli(): void
    {
        $application = Application::instance();

        $application->bootEngine(new ConsoleEngine());

        $application->bootApplication($this->makeConsoleModule());

        /** @var Module $fakeSecondaryModule */
        $fakeSecondaryModule = $this->createMock(MvcModule::class);
        $application->bootModule($fakeSecondaryModule);

        $output = $application->run(new ConsoleInput(['command', '--arg1', '--arg2']));

        $this->assertInstanceOf(ConsoleOutput::class, $output);
    }

    /** @test */
    public function runCliWithoutConsoleEngine(): void
    {
        $application = Application::instance();

        $application->bootEngine(new MvcEngine());

        $application->bootApplication($this->makeMvcModule());

        $output = $application->run(new ConsoleInput(['command', '--arg1', '--arg2']));

        $this->assertInstanceOf(ConsoleOutput::class, $output);
        $this->assertSame(Terminal::STATUS_ERROR, $output->getStatusCode());
        $this->assertSame(
            'There is no engine capable of interpreting terminal commands',
            $output->getBody()
        );
    }

    /** @test */
    public function runWeb(): void
    {
        $application = Application::instance();

        $application->bootEngine(new MvcEngine());

        $application->bootApplication($this->makeMvcModule());

        $output = $application->run(
            (new DiactorosHttpFactory())->createRequestFromGlobals()
        );

        $this->assertInstanceOf(ResponseInterface::class, $output);
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    private function makeConsoleModule(): ConsoleModule
    {
        return new class extends ConsoleModule
        {
            public function bootDependencies(Container $container): void
            {
                // ...
            }

            public function bootRoutineDirectories(RoutineSourceSet &$sourceSet): void
            {
                $sourceSet->add(new RoutineSource(__DIR__));
            }

            public function getCommandName(): string
            {
                return 'test-script';
            }

            /** Devolve o diretório real da aplicação que implementa o Console */
            public function getCommandPath(): string
            {
                return __DIR__;
            }
        };
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    private function makeMvcModule(): MvcModule
    {
        return new class extends MvcModule
        {
            public function bootDependencies(Container $container): void
            {
                $container->addFactory(Session::class, new SymfonyNativeSession());
                $container->addFactory(HttpFactory::class, new DiactorosHttpFactory());
            }

            public function bootRoutes(Router &$router): void
            {
                $router->any('/')->usingAction(ArrayObject::class);
            }
        };
    }
}
