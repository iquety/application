<?php

declare(strict_types=1);

namespace Tests\IoEngine;

use InvalidArgumentException;
use Iquety\Application\IoEngine\IoEngine;
use Iquety\Application\IoEngine\EngineSet;
use Iquety\Application\IoEngine\FrontController\Command\NotFoundCommand;
use Iquety\Application\IoEngine\FrontController\FcEngine;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Action\ActionDescriptor;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Injection\Container;
use Tests\IoEngine\FrontController\Stubs\SubDirectory\TwoCommand;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EngineSetSingleTest extends TestCase
{
    /** @test */
    public function duplicatedEngine(): void
    {
        /** @var IoEngine $engine */
        $engine = $this->createMock(IoEngine::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Engine %s has already been registered',
            $engine::class
        ));

        $engineSet = new EngineSet($this->makeContainer());

        $engineSet->add($engine);
        $engineSet->add($engine);
    }

    /** @test */
    public function distinctEngines(): void
    {
        /** @var IoEngine $engineOne */
        $engineOne = $this->createMock(IoEngine::class);

        /** @var IoEngine $engineTwo */
        $engineTwo = $this->createMock(IoEngine::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Engine %s has already been registered',
            $engineOne::class
        ));

        $engineSet = new EngineSet($this->makeContainer());

        $engineSet->add($engineOne);
        $engineSet->add($engineTwo);

        $this->assertCount(2, $engineSet->toArray());
    }

    /** @test */
    public function singleEngineResolved(): void
    {
        $container = new Container();
        $moduleSet = new ModuleSet();

        $engine = new FcEngine();
        $engine->useModuleSet($moduleSet);

        $module = $this->makeFcModuleOne('Tests\IoEngine\FrontController\Stubs');

        $engineSet = new EngineSet($container);
        $engineSet->add($engine);

        $moduleSet->add($module);
        $engine->boot($module);

        $descriptor = $engineSet->resolve(Input::fromString('sub-directory/two-command'));

        $this->assertSame($module::class, $descriptor->module());
        $this->assertSame(TwoCommand::class . '::execute', $descriptor->action());
    }

    /** @test */
    public function singleEngineNotFound(): void
    {
        $container = $this->makeContainer();
        $moduleSet = new ModuleSet();

        $engine = new FcEngine();
        $engine->useModuleSet($moduleSet);

        $module = $this->makeFcModuleOne('Tests\IoEngine\FrontController\Stubs');

        $engineSet = new EngineSet($container);
        $engineSet->add($engine);

        $moduleSet->add($module);
        $engine->boot($module);

        $descriptor = $engineSet->resolve(Input::fromString('invalid'));

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);
        $this->assertSame('not-found', $descriptor->module());
        $this->assertSame(NotFoundCommand::class . '::execute', $descriptor->action());
    }
}
