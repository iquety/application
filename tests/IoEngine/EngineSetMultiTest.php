<?php

declare(strict_types=1);

namespace Tests\IoEngine;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\EngineSet;
use Iquety\Application\IoEngine\FrontController\FcEngine;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Http\HttpMethod;
use Iquety\Injection\Container;
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
        $container = $this->makeContainer();
        $moduleSet = new ModuleSet();

        $made = $this->makeEngineSet($container, $moduleSet);

        $descriptor = $made->engineSet->resolve(
            Input::fromString($uri)
        );

        $bootstrap = match ($dependency) {
            'fc-dep-one'  => $made->fcModuleOne::class,
            'fc-dep-two'  => $made->fcModuleTwo::class,
            'mvc-dep-one' => $made->mvcModuleOne::class,
            'mvc-dep-two' => $made->mvcModuleTwo::class,
            default       => ''
        };

        $className = match ($dependency) {
            'fc-dep-one'  => TwoCommand::class,
            'fc-dep-two'  => TwoCommand::class,
            'mvc-dep-one' => OneController::class,
            'mvc-dep-two' => OneController::class,
            default       => ''
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

        $fcModuleOne = $this->makeFcModuleOne(
            'Tests\IoEngine\FrontController\Stubs',
            ['fc-dep-one' => fn() => 'one']
        );

        $fcModuleTwo = $this->makeFcModuleTwo(
            'Tests\IoEngine\FrontController\Stubs\SubDirectory',
            ['fc-dep-two' => fn() => 'two']
        );

        $mvcModuleOne = $this->makeMvcModuleOne(
            HttpMethod::GET,
            '/mvc-one/:id',
            OneController::class . '@execute',
            ['mvc-dep-one' => fn() => 'one']
        );

        $mvcModuleTwo = $this->makeMvcModuleTwo(
            HttpMethod::GET,
            '/mvc-two/:id',
            OneController::class . '@execute',
            ['mvc-dep-two' => fn() => 'two']
        );

        $engineOne = $this->makeFcEngine(
            $container,
            $moduleSet,
            [ $fcModuleOne, $fcModuleTwo ]
        );

        $engineTwo = $this->makeMvcEngine(
            $container,
            $moduleSet,
            [ $mvcModuleOne, $mvcModuleTwo ]
        );

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
}
