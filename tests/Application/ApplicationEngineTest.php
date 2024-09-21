<?php

declare(strict_types=1);

namespace Tests\Application;

use InvalidArgumentException;
use Iquety\Application\Application;
use Iquety\Application\IoEngine\FrontController\FcEngine;
use Iquety\Application\IoEngine\IoEngine;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ApplicationEngineTest extends TestCase
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
    public function bootEngines(): void
    {
        /** @var Module */
        $module = $this->createStub(Module::class);

        /** @var IoEngine */
        $engineOne = $this->createStub(IoEngine::class);
        
        /** @var IoEngine */
        $engineTwo = $this->createStub(FcEngine::class);

        /** @var IoEngine */
        $engineThree = $this->createStub(MvcEngine::class);

        $application = Application::instance();

        // inicializa o módulo principal
        $application->bootApplication($module);

        $application->bootEngine($engineOne);
        $application->bootEngine($engineTwo);
        $application->bootEngine($engineThree);

        $engineList = $application->engineSet()->toArray();

        $this->assertCount(3, $engineList);
        
        $this->assertInstanceOf(IoEngine::class, $engineList[$engineOne::class]);
    }

    /** @test */
    public function bootTwoEqualEngines(): void
    {
        /** @var Module */
        $module = $this->createStub(Module::class);

        /** @var IoEngine */
        $engine = $this->createStub(IoEngine::class);
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Engine %s has already been registered',
            $engine::class
        ));
                
        $application = Application::instance();

        // inicializa o módulo principal
        $application->bootApplication($module);
        
        $application->bootEngine($engine);
        $application->bootEngine($engine);
    }
}
