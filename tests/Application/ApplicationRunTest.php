<?php

declare(strict_types=1);

namespace Tests\Application;

use Exception;
use Iquety\Application\Application;
use Iquety\Application\IoEngine\Console\ConsoleDescriptor;
use Iquety\Application\IoEngine\Console\ConsoleEngine;
use Iquety\Application\IoEngine\Console\ConsoleInput;
use Iquety\Application\IoEngine\Console\ConsoleOutput;
use Iquety\Application\IoEngine\IoEngine;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Application\IoEngine\Mvc\MvcModule;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ApplicationRunTest extends TestCase
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
    public function runWithoutEngine(): void
    {
        /** @var ServerRequestInterface $fakeRequest */
        $fakeRequest = $this->createMock(ServerRequestInterface::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'No I/O motors were registered in the application'
        );

        $application = Application::instance();

        $application->run($fakeRequest);
    }

    /** @test */
    public function runWithoutModule(): void
    {
        /** @var ServerRequestInterface $fakeRequest */
        $fakeRequest = $this->createMock(ServerRequestInterface::class);

        /** @var IoEngine $fakeEngine */
        $fakeEngine = $this->createMock(IoEngine::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'No module was registered in the application'
        );

        $application = Application::instance();

        $application->bootEngine($fakeEngine);

        $application->run($fakeRequest);
    }

    /** @test */
    public function runWithMainModuleBootError(): void
    {
        /** @var ServerRequestInterface $fakeRequest */
        $fakeRequest = $this->createMock(ServerRequestInterface::class);

        /** @var IoEngine $fakeEngine */
        $fakeEngine = $this->createMock(IoEngine::class);

        $fakeModule = $this->createMock(Module::class);

        $fakeModule->method('bootDependencies')
            ->willThrowException(new Exception('Erro qualquer'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            'Method bootApplication failed for module %s',
            $fakeModule::class
        ));

        $application = Application::instance();

        $application->bootEngine($fakeEngine);

        /** @var Module $fakeModule */
        $application->bootApplication($fakeModule);

        $application->run($fakeRequest);
    }

    /** @test */
    public function runWithSecondaryModuleBootError(): void
    {
        /** @var ServerRequestInterface $fakeRequest */
        $fakeRequest = $this->createMock(ServerRequestInterface::class);

        /** @var IoEngine $fakeEngine */
        $fakeEngine = $this->createMock(IoEngine::class);

        $fakeMainModule = $this->createMock(Module::class);

        $fakeSecondaryModule = $this->createMock(MvcModule::class);
        $fakeSecondaryModule->method('bootDependencies')
            ->willThrowException(new Exception('Erro qualquer'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            'Method bootModule failed for module %s',
            $fakeSecondaryModule::class
        ));

        $application = Application::instance();

        $application->bootEngine($fakeEngine);

        /** @var Module $fakeMainModule */
        $application->bootApplication($fakeMainModule);

        /** @var Module $fakeSecondaryModule */
        $application->bootModule($fakeSecondaryModule);

        $application->run($fakeRequest);
    }

    /** @test */
    public function runWithEngineBootError(): void
    {
        /** @var ServerRequestInterface $fakeRequest */
        $fakeRequest = $this->createMock(ServerRequestInterface::class);

        $fakeEngine = $this->createMock(IoEngine::class);

        $fakeEngine->method('boot')
            ->willThrowException(new Exception('Erro qualquer'));

        /** @var Module $fakeMainModule */
        $fakeMainModule = $this->createMock(Module::class);

        /** @var Module $fakeSecondaryModule */
        $fakeSecondaryModule = $this->createMock(MvcModule::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            'Method boot failed for engine %s',
            $fakeEngine::class
        ));

        $application = Application::instance();

        /** @var IoEngine $fakeEngine */
        $application->bootEngine($fakeEngine);

        $application->bootApplication($fakeMainModule);

        $application->bootModule($fakeSecondaryModule);

        $application->run($fakeRequest);
    }
}
