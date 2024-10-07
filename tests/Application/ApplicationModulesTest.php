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
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    public function tearDown(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function bootModules(): void
    {
        /** @var Module */
        $moduleOne = $this->createStub(Module::class);

        /** @var Module */
        $moduleTwo = $this->createStub(FcModule::class);

        /** @var Module */
        $moduleThree = $this->createStub(MvcModule::class);

        $application = Application::instance();

        // inicializa o módulo principal
        $application->bootApplication($moduleOne);

        // inicializa os módulos secundários
        $application->bootModule($moduleTwo);
        $application->bootModule($moduleThree);

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

        /** @var Module */
        $moduleTwo = $this->createStub(FcModule::class);

        /** @var Module */
        $moduleThree = $this->createStub(MvcModule::class);

        $application = Application::instance();

        // inicializa os módulos secundários
        $application->bootModule($moduleTwo);
        $application->bootModule($moduleThree);

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
        /** @var Module */
        $moduleOne = $this->createStub(Module::class);

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
        $moduleOne = $this->createStub(Module::class);

        /** @var Module */
        $moduleTwo = $this->createStub(FcModule::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Module %s has already been registered',
            $moduleTwo::class
        ));

        $application = Application::instance();

        // inicializa o módulo principal
        $application->bootApplication($moduleOne);

        // inicializa os módulos secundários
        $application->bootModule($moduleTwo);
        $application->bootModule($moduleTwo);
    }
}
