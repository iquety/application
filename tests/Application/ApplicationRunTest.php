<?php

declare(strict_types=1);

namespace Tests\Application;

use Exception;
use Iquety\Application\Application;
use Iquety\Application\IoEngine\IoEngine;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\Mvc\MvcModule;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ApplicationRunTest extends TestCase
{
    /** @test */
    public function runWithoutEngine(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'No I/O motors were registered in the application'
        );

        $application = Application::instance();

        $application->run($this->makeServerRequest());
    }

    /** @test */
    public function runWithoutModule(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'No module was registered in the application'
        );

        $application = Application::instance();

        $application->bootEngine($this->makeGenericIoEngine());

        $application->run($this->makeServerRequest());
    }

    /** @test */
    public function runWithMainModuleBootError(): void
    {
        $fakeModule = $this->createMock(Module::class);

        $fakeModule->method('bootDependencies')
            ->willThrowException(new Exception('Erro qualquer'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            'Method bootApplication failed for module %s',
            $fakeModule::class
        ));

        $application = Application::instance();

        $application->bootEngine($this->makeGenericIoEngine());

        /** @var Module $fakeModule */
        $application->bootApplication($fakeModule);

        $application->run($this->makeServerRequest());
    }

    /** @test */
    public function runWithSecondaryModuleBootError(): void
    {
        $fakeSecondaryModule = $this->createMock(MvcModule::class);
        $fakeSecondaryModule->method('bootDependencies')
            ->willThrowException(new Exception('Erro qualquer'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            'Method bootModule failed for module %s',
            $fakeSecondaryModule::class
        ));

        $application = Application::instance();

        $application->bootEngine($this->makeGenericIoEngine());

        $application->bootApplication($this->makeGenericModule());

        /** @var Module $fakeSecondaryModule */
        $application->bootModule($fakeSecondaryModule);

        $application->run($this->makeServerRequest());
    }

    /** @test */
    public function runWithEngineBootError(): void
    {
        $fakeEngine = $this->createMock(IoEngine::class);

        $fakeEngine->method('boot')
            ->willThrowException(new Exception('Erro qualquer'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            'Method boot failed for engine %s',
            $fakeEngine::class
        ));

        $application = Application::instance();

        /** @var IoEngine $fakeEngine */
        $application->bootEngine($fakeEngine);

        $application->bootApplication($this->makeGenericModule());

        $application->bootModule($this->makeMvcModuleOne());

        $application->run($this->makeServerRequest());
    }
}
