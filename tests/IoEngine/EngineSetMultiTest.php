<?php

declare(strict_types=1);

namespace Tests\IoEngine;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\EngineSet;
use Iquety\Application\IoEngine\FrontController\FcModule;
use Iquety\Application\IoEngine\FrontController\FcEngine;
use Iquety\Application\IoEngine\FrontController\CommandSource;
use Iquety\Application\IoEngine\FrontController\CommandSourceSet;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Application\IoEngine\Mvc\MvcModule;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use Tests\IoEngine\Mvc\Stubs\OneController;
use Tests\IoEngine\FrontController\Stubs\SubDirectory\TwoCommand;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EngineSetMultiTest extends TestCase
{
    /** @return array<string,array<int,mixed>> */
    public function requestToMultiProvider(): array
    {
        $list = [];

        $list['front controller boot one'] = ['sub-directory/two-command', 'fc-dep-one', 'one'];
        $list['front controller boot two'] = ['two-command', 'fc-dep-two', 'two'];
        $list['mvc boot one'] = ['mvc-one/22', 'mvc-dep-one', 'one'];
        $list['mvc boot two'] = ['mvc-two/22', 'mvc-dep-two', 'two'];

        return $list;
    }

    /**
     * @test
     * @dataProvider requestToMultiProvider
     */
    public function multiEngineResolved(string $uri, string $dependency, string $value): void
    {
        $container = new Container();
        $moduleSet = new ModuleSet();

        $made = $this->makeEngineSet($container, $moduleSet);

        $descriptor = $made->engineSet->resolve(Input::fromString($uri));

        $bootstrap = match ($dependency) {
            'fc-dep-one' => $made->fcModuleOne::class,
            'fc-dep-two' => $made->fcModuleTwo::class,
            'mvc-dep-one' => $made->mvcModuleOne::class,
            'mvc-dep-two' => $made->mvcModuleTwo::class,
            default => ''
        };

        $className = match ($dependency) {
            'fc-dep-one' => TwoCommand::class,
            'fc-dep-two' => TwoCommand::class,
            'mvc-dep-one' => OneController::class,
            'mvc-dep-two' => OneController::class,
            default => ''
        };

        $this->assertSame($bootstrap, $descriptor->module());
        $this->assertSame($className . '::execute', $descriptor->action());

        $this->assertSame($value, $container->get($dependency));
    }

    // toolset

    /**
     * @return object{
     *   'engineSet':EngineSet,
     *   'fcModuleOne':Module,
     *   'fcModuleTwo':Module,
     *   'mvcModuleOne':Module,
     *   'mvcModuleTwo':Module
     * }
     */
    private function makeEngineSet(Container $container, ModuleSet $moduleSet): object
    {
        $engineSet = new EngineSet($container);

        $engineOne = $this->makeFcEngine($container, $moduleSet, [
            $fcModuleOne = $this->makeFcModuleOne(),
            $fcModuleTwo = $this->makeFcModuleTwo()
        ]);

        $engineTwo = $this->makeMvcEngine($container, $moduleSet, [
            $mvcModuleOne = $this->makeMvcModuleOne(),
            $mvcModuleTwo = $this->makeMvcModuleTwo()
        ]);

        $engineSet->add($engineOne);
        $engineSet->add($engineTwo);

        return (object)[
            'engineSet'    => $engineSet,
            'fcModuleOne'  => $fcModuleOne,
            'fcModuleTwo'  => $fcModuleTwo,
            'mvcModuleOne' => $mvcModuleOne,
            'mvcModuleTwo' => $mvcModuleTwo,
        ];
    }

    /** @param array<int,Module> $modulesBootstrap */
    private function makeFcEngine(Container $container, ModuleSet $moduleSet, array $modulesBootstrap): FcEngine
    {
        $engine = new FcEngine();
        $engine->useContainer($container);
        $engine->useModuleSet($moduleSet);

        foreach ($modulesBootstrap as $module) {
            $moduleSet->add($module);

            $engine->boot($module);
        }

        return $engine;
    }

    /** @param array<int,Module> $modulesBootstrap */
    private function makeMvcEngine(Container $container, ModuleSet $moduleSet, array $modulesBootstrap): MvcEngine
    {
        $engine = new MvcEngine();
        $engine->useContainer($container);
        $engine->useModuleSet($moduleSet);

        foreach ($modulesBootstrap as $module) {
            $moduleSet->add($module);

            $engine->boot($module);
        }

        return $engine;
    }

    private function makeFcModuleOne(): FcModule
    {
        return new class extends FcModule {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('fc-dep-one', fn() => 'one');
            }

            public function bootNamespaces(CommandSourceSet &$sourceSet): void
            {
                $sourceSet->add(new CommandSource(
                    'Tests\IoEngine\FrontController\Stubs'
                ));
            }
        };
    }

    private function makeFcModuleTwo(): FcModule
    {
        return new class extends FcModule {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('fc-dep-two', fn() => 'two');
            }

            public function bootNamespaces(CommandSourceSet &$sourceSet): void
            {
                $sourceSet->add(new CommandSource(
                    'Tests\IoEngine\FrontController\Stubs\SubDirectory'
                ));
            }
        };
    }

    private function makeMvcModuleOne(): MvcModule
    {
        return new class extends MvcModule {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('mvc-dep-one', fn() => 'one');
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/mvc-one/:id')->usingAction(OneController::class, 'execute');
            }
        };
    }

    private function makeMvcModuleTwo(): MvcModule
    {
        return new class extends MvcModule {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('mvc-dep-two', fn() => 'two');
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/mvc-two/:id')->usingAction(OneController::class, 'execute');
            }
        };
    }
}
