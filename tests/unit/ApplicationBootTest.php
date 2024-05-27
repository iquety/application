<?php

declare(strict_types=1);

namespace Tests\Unit;

use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Application;

class ApplicationBootTest extends ApplicationCase
{
    /** @test */
    public function getInstance(): void
    {
        $this->assertSame(
            Application::instance(),
            Application::instance()
        );
    }    

    /** @test */
    public function bootEngines(): void
    {
        $instance = Application::instance();

        $fcEngine = new FcEngine();
        $mvcEngine = new MvcEngine();

        $instance->bootEngine($fcEngine);
        $instance->bootEngine($mvcEngine);

        $engineList = $instance->engineSet()->toArray();
        $this->assertCount(2, $engineList);
        $this->assertInstanceOf($fcEngine::class, $engineList[$fcEngine::class]);
        $this->assertInstanceOf($mvcEngine::class, $engineList[$mvcEngine::class]);
    }
    
    /** @test */
    public function bootModules(): void
    {
        $instance = Application::instance();

        // boot da aplicação

        $bootstrapOne = $this->makeFcBootstrapOne();
        $instance->bootApplication($bootstrapOne);

        $moduleList = $instance->moduleSet()->toArray();
        $this->assertCount(1, $moduleList);
        $this->assertInstanceOf($bootstrapOne::class, $moduleList[$bootstrapOne::class]);

        // modulo adicional 1 

        $bootstrapTwo = $this->makeFcBootstrapTwo();
        $instance->bootModule($bootstrapTwo);

        $moduleList = $instance->moduleSet()->toArray();
        $this->assertCount(2, $moduleList);
        $this->assertInstanceOf($bootstrapTwo::class, $moduleList[$bootstrapTwo::class]);

        // modulo adicional 2

        $bootstrapThree = $this->makeMvcBootstrapOne();
        $instance->bootModule($bootstrapThree);

        $moduleList = $instance->moduleSet()->toArray();
        $this->assertCount(3, $moduleList);
        $this->assertInstanceOf($bootstrapThree::class, $moduleList[$bootstrapThree::class]);

        // modulo adicional 3

        $bootstrapFour = $this->makeMvcBootstrapTwo();
        $instance->bootModule($bootstrapFour);

        $moduleList = $instance->moduleSet()->toArray();
        $this->assertCount(4, $moduleList);
        $this->assertInstanceOf($bootstrapFour::class, $moduleList[$bootstrapFour::class]);
    }
}
