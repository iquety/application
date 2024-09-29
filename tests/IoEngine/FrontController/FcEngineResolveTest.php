<?php

declare(strict_types=1);

namespace Tests\IoEngine\FrontController;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\FrontController\CommandSource;
use Iquety\Application\IoEngine\FrontController\CommandSourceSet;
use Iquety\Application\IoEngine\FrontController\FcEngine;
use Iquety\Application\IoEngine\FrontController\FcSourceHandler;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Injection\Container;
use RuntimeException;
use Tests\TestCase;

class FcEngineResolveTest extends TestCase
{
    /** @test */
    public function resolveWithoutModules(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('At least one module must be provided');

        $sourceSet = new CommandSourceSet(Module::class);
        $sourceSet->add(new CommandSource('Tests\IoEngine\FrontController\Stubs'));

        $handler = new FcSourceHandler();
        $handler->addSources($sourceSet);

        $container = new Container();
        $container->addSingleton(FcSourceHandler::class, $handler);

        $engine = new FcEngine();
        $engine->useContainer($container);
        $engine->useModuleSet(new ModuleSet);

        $engine->resolve(Input::fromString('/any-command'));
    }
}
