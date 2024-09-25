<?php

declare(strict_types=1);

namespace Tests\IoEngine\Console;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Console\ConsoleSourceHandler;
use Iquety\Application\IoEngine\Console\RoutineSource;
use Iquety\Application\IoEngine\Console\RoutineSourceSet;
use Iquety\Application\IoEngine\Module;
use Iquety\Console\Routine;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConsoleSourceHandlerTest extends TestCase
{
    /** @test */
    public function defaultValues(): void
    {
        $input = Input::fromConsoleArguments(['script-name']);
        $handler = new ConsoleSourceHandler();

        $this->assertSame('unknown', $handler->getCommandName());
        $this->assertSame('', $handler->getCommandPath());
        $this->assertFalse($handler->hasSources());
        $this->assertSame([], $handler->getDirectoryList());

        $this->assertEquals(
            Routine::class,
            $handler->getDescriptorTo($input)->type()
        );

        $this->assertEquals(
            '',
            $handler->getDescriptorTo($input)->module()
        );

        $this->assertEquals(
            '::',
            $handler->getDescriptorTo($input)->action()
        );
    }

    /** @test */
    public function settedValues(): void
    {
        $input = Input::fromConsoleArguments(['script-name']);

        $sourceSet = new RoutineSourceSet(Module::class);

        $handler = new ConsoleSourceHandler();

        $handler->setCommandName('script-name');
        $handler->setCommandPath(__DIR__);

        $handler->addSources($sourceSet);

        $this->assertSame('script-name', $handler->getCommandName());
        $this->assertSame(__DIR__, $handler->getCommandPath());
        $this->assertFalse($handler->hasSources());
        $this->assertSame([], $handler->getDirectoryList());

        $this->assertEquals(
            Routine::class,
            $handler->getDescriptorTo($input)->type()
        );

        $this->assertEquals(
            Module::class,
            $handler->getDescriptorTo($input)->module()
        );

        $this->assertEquals(
            '::',
            $handler->getDescriptorTo($input)->action()
        );
    }

    /** @test */
    public function getDirectoryList(): void
    {
        $sourceSet = new RoutineSourceSet(Module::class);
        $sourceSet->add(new RoutineSource(__DIR__));

        $handler = new ConsoleSourceHandler();

        $handler->addSources($sourceSet);

        $this->assertTrue($handler->hasSources());

        $this->assertSame([__DIR__], $handler->getDirectoryList());
    }

    /** @return array<string,array<int,string>> */
    public function unusedMethodsProvider(): array
    {
        $list = [];
        
        $list['getErrorDescriptor']     = ['getErrorDescriptor'];
        $list['getMainDescriptor']      = ['getMainDescriptor'];
        $list['getNotFoundDescriptor']  = ['getNotFoundDescriptor'];
        $list['setErrorActionClass']    = ['setErrorActionClass'];
        $list['setMainActionClass']     = ['setMainActionClass'];
        $list['setNotFoundActionClass'] = ['setNotFoundActionClass'];

        return $list;
    }

    /**
     * @test
     * @dataProvider unusedMethodsProvider
     */
    public function unusedMethods(string $methodName): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The Console engine does not use this method');

        $handler = new ConsoleSourceHandler();

        $handler->{$methodName}('');
    }

    // /** @test */
    // public function descriptorClosure(): void
    // {
    //     $router = new Router();
    //     $router->forModule(MvcBootstrap::class);

    //     $router->get('/user/:id')->usingAction(fn() => 'execute closure');

    //     $handler = new MvcSourceHandler();

    //     $handler->addRouter($router);

    //     $descriptor = $handler->getDescriptorTo(Input::fromString('user/22'));

    //     $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

    //     $callable = $descriptor->action();

    //     $this->assertInstanceOf(Closure::class, $callable);

    //     $this->assertSame('execute closure', $callable());
    // }
}