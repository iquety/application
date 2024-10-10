<?php

declare(strict_types=1);

namespace Tests\Application;

use InvalidArgumentException;
use Iquety\Application\Application;
use Iquety\Application\IoEngine\FrontController\FcModule;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\Mvc\MvcModule;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ApplicationModulesTest extends TestCase
{
    /** @test */
    public function notBootApplication(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Main module was not specified');

        $application = Application::instance();

        $application->mainModule();
    }

    /** @test */
    public function bootModules(): void
    {
        /** @var Module */
        $moduleOne = $this->createStub(Module::class);

        $application = Application::instance();

        // inicializa o módulo principal
        $application->bootApplication($moduleOne);

        // inicializa os módulos secundários
        $application->bootModule($this->makeFcModuleOne());
        $application->bootModule($this->makeMvcModuleOne());

        $moduleList = $application->moduleSet()->toArray();

        $this->assertCount(3, $moduleList);

        $this->assertInstanceOf(Module::class, $moduleList[$moduleOne::class]);
        $this->assertSame($application->mainModule(), $moduleList[$moduleOne::class]);
    }

    /** @test */
    public function bootModulesInverted(): void
    {
        /** @var Module */
        $moduleOne = $this->createStub(Module::class);

        $application = Application::instance();

        // inicializa os módulos secundários
        $application->bootModule($this->makeFcModuleOne());
        $application->bootModule($this->makeMvcModuleOne());

        // inicializa o módulo principal
        $application->bootApplication($moduleOne);

        $moduleList = $application->moduleSet()->toArray();

        $this->assertCount(3, $moduleList);

        $this->assertInstanceOf(Module::class, $moduleList[$moduleOne::class]);
        $this->assertSame($application->mainModule(), $moduleList[$moduleOne::class]);
    }

    /** @test */
    public function bootModulesEqualApplication(): void
    {
        $moduleOne = $this->makeGenericModule();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Module %s has already been registered',
            $moduleOne::class
        ));

        $application = Application::instance();

        // inicializa o módulo principal
        $application->bootApplication($moduleOne);

        // inicializa os módulos secundários
        $application->bootModule($moduleOne);
    }

    /** @test */
    public function bootTwoEqualModules(): void
    {
        /** @var Module */
        $moduleTwo = $this->createStub(FcModule::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Module %s has already been registered',
            $moduleTwo::class
        ));

        $application = Application::instance();

        // inicializa o módulo principal
        $application->bootApplication($this->makeGenericModule());

        // inicializa os módulos secundários
        $application->bootModule($moduleTwo);
        $application->bootModule($moduleTwo);
    }
}
