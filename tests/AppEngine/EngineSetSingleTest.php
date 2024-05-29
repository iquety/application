<?php

declare(strict_types=1);

namespace Tests\AppEngine;

use InvalidArgumentException;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\EngineSet;
use Iquety\Application\AppEngine\FrontController\Command\NotFoundCommand;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\FrontController\Source;
use Iquety\Application\AppEngine\FrontController\SourceSet;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Injection\Container;
use Tests\AppEngine\FrontController\Stubs\Commands\SubDirectory\TwoCommand;
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
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The same engine cannot be added twice');

        $container = new Container();

        $engineSet = new EngineSet($container);

        $engine = $this->createMock(AppEngine::class);

        $engineSet->add($engine);
        $engineSet->add($engine);
    }

    /** @test */
    public function distinctEngines(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The same engine cannot be added twice');

        $container = new Container();

        $engineSet = new EngineSet($container);

        $engineOne = $this->createMock(AppEngine::class);
        $engineTwo = $this->createMock(AppEngine::class);

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

        $bootstrap = new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('signature-test', fn() => 'teste');
            }

            public function bootNamespaces(SourceSet &$sourceSet): void
            {
                $sourceSet->add(new Source(
                    'Tests\AppEngine\FrontController\Stubs\Commands'
                ));
            }
        };

        $engineSet = new EngineSet($container);
        $engineSet->add($engine);

        $moduleSet->add($bootstrap);
        $engine->boot($bootstrap);

        $descriptor = $engineSet->resolve(Input::fromString('sub-directory/two-command'));

        $this->assertSame($bootstrap::class, $descriptor->module());
        $this->assertSame(TwoCommand::class . '::execute', $descriptor->action());
    }

    /** @test */
    public function singleEngineNotFound(): void
    {
        $container = new Container();
        $moduleSet = new ModuleSet();

        $engine = new FcEngine();
        $engine->useModuleSet($moduleSet);

        $bootstrap = new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('signature-test', fn() => 'teste');
            }

            public function bootNamespaces(SourceSet &$sourceSet): void
            {
                $sourceSet->add(new Source(
                    'Tests\AppEngine\FrontController\Stubs\Commands'
                ));
            }
        };

        $engineSet = new EngineSet($container);
        $engineSet->add($engine);

        $moduleSet->add($bootstrap);
        $engine->boot($bootstrap);

        $descriptor = $engineSet->resolve(Input::fromString('invalid'));

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);
        $this->assertSame('not-found', $descriptor->module());
        $this->assertSame(NotFoundCommand::class . '::execute', $descriptor->action());
    }
}
