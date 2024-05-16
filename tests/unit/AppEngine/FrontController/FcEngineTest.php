<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\Directory;
use Iquety\Application\AppEngine\FrontController\DirectorySet;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\Input;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Injection\Container;
use Tests\Unit\TestCase;

class FcEngineTest extends TestCase
{
    /** @test */
    public function bootEngine(): void
    {
        $container = new Container();
        $moduleSet = new ModuleSet();

        $engine = new FcEngine();
        $engine->useContainer($container);
        $engine->useModuleSet($moduleSet);

        $bootstrap = new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('signature-test', fn() => 'teste');
            }

            public function bootDirectories(DirectorySet &$directorySet): void
            {
                $directorySet->add(new Directory(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
                    __DIR__ . "/Stubs/Commands"
                ));
            }
        };

        $engine->boot($bootstrap);

        $responseDescriptor = $engine->resolve(Input::fromString('/one/two'));

        var_dump($responseDescriptor);
        exit;

        // var_dump($engine->moduleSet()->findByClass($bootstrap::class));
        exit;

        var_dump($container->get('signature-test'));
        exit;
    }

    // /** @test */
    // public function duplicatedEngine(): void
    // {
    //     $this->expectException(InvalidArgumentException::class);
    //     $this->expectExceptionMessage('The same engine cannot be added twice');

    //     $container = new Container();

    //     $engineSet = new EngineSet($container);

    //     $engine = $this->createMock(AppEngine::class);

    //     $engineSet->add($engine);
    //     $engineSet->add($engine);
    // }

    // /** @test */
    // public function addEngines(): void
    // {
    //     $this->expectException(InvalidArgumentException::class);
    //     $this->expectExceptionMessage('The same engine cannot be added twice');
        
    //     $container = new Container();

    //     $engineSet = new EngineSet($container);

    //     $engineOne = $this->createMock(AppEngine::class);
    //     $engineTwo = $this->createMock(AppEngine::class);

    //     $engineSet->add($engineOne);
    //     $engineSet->add($engineTwo);

    //     $this->assertCount(2, $engineSet->toArray());
    // }
}
