<?php

declare(strict_types=1);

namespace Tests\AppEngine;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\Bootstrap;
use Iquety\Application\AppEngine\EngineSet;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\FrontController\Source;
use Iquety\Application\AppEngine\FrontController\SourceSet;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use Tests\AppEngine\FrontController\Stubs\Commands\SubDirectory\TwoCommand;
use Tests\AppEngine\Mvc\Stubs\OneController;
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
            'fc-dep-one' => $made->fcBootstrapOne::class,
            'fc-dep-two' => $made->fcBootstrapTwo::class,
            'mvc-dep-one' => $made->mvcBootstrapOne::class,
            'mvc-dep-two' => $made->mvcBootstrapTwo::class,
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
     *   'fcBootstrapOne':Bootstrap,
     *   'fcBootstrapTwo':Bootstrap,
     *   'mvcBootstrapOne':Bootstrap,
     *   'mvcBootstrapTwo':Bootstrap
     * }
     */
    private function makeEngineSet(Container $container, ModuleSet $moduleSet): object
    {
        $engineSet = new EngineSet($container);

        $engineOne = $this->makeFcEngine($container, $moduleSet, [
            $fcBootstrapOne = $this->makeFcBootstrapOne(),
            $fcBootstrapTwo = $this->makeFcBootstrapTwo()
        ]);

        $engineTwo = $this->makeMvcEngine($container, $moduleSet, [
            $mvcBootstrapOne = $this->makeMvcBootstrapOne(),
            $mvcBootstrapTwo = $this->makeMvcBootstrapTwo()
        ]);

        $engineSet->add($engineOne);
        $engineSet->add($engineTwo);

        return (object)[
            'engineSet'       => $engineSet,
            'fcBootstrapOne'  => $fcBootstrapOne,
            'fcBootstrapTwo'  => $fcBootstrapTwo,
            'mvcBootstrapOne' => $mvcBootstrapOne,
            'mvcBootstrapTwo' => $mvcBootstrapTwo,
        ];
    }

    /** @param array<int,Bootstrap> $modulesBootstrap */
    private function makeFcEngine(Container $container, ModuleSet $moduleSet, array $modulesBootstrap): FcEngine
    {
        $engine = new FcEngine();
        $engine->useContainer($container);
        $engine->useModuleSet($moduleSet);

        foreach ($modulesBootstrap as $bootstrap) {
            $moduleSet->add($bootstrap);

            $engine->boot($bootstrap);
        }

        return $engine;
    }

    /** @param array<int,Bootstrap> $modulesBootstrap */
    private function makeMvcEngine(Container $container, ModuleSet $moduleSet, array $modulesBootstrap): MvcEngine
    {
        $engine = new MvcEngine();
        $engine->useContainer($container);
        $engine->useModuleSet($moduleSet);

        foreach ($modulesBootstrap as $bootstrap) {
            $moduleSet->add($bootstrap);

            $engine->boot($bootstrap);
        }

        return $engine;
    }

    private function makeFcBootstrapOne(): FcBootstrap
    {
        return new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('fc-dep-one', fn() => 'one');
            }

            public function bootNamespaces(SourceSet &$sourceSet): void
            {
                $sourceSet->add(new Source(
                    'Tests\AppEngine\FrontController\Stubs\Commands'
                ));
            }
        };
    }

    private function makeFcBootstrapTwo(): FcBootstrap
    {
        return new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('fc-dep-two', fn() => 'two');
            }

            public function bootNamespaces(SourceSet &$sourceSet): void
            {
                $sourceSet->add(new Source(
                    'Tests\AppEngine\FrontController\Stubs\Commands\SubDirectory'
                ));
            }
        };
    }

    private function makeMvcBootstrapOne(): MvcBootstrap
    {
        return new class extends MvcBootstrap {
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

    private function makeMvcBootstrapTwo(): MvcBootstrap
    {
        return new class extends MvcBootstrap {
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
