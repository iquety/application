<?php

declare(strict_types=1);

namespace Tests\IoEngine\Console;

use Iquety\Application\IoEngine\Console\ConsoleModule;
use Iquety\Application\IoEngine\Console\NotImplementedException;
use Iquety\Injection\Container;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ConsoleModuleTest extends TestCase
{
    public function methodsProvider(): array
    {
        $list = [];

        $list['getActionType'] = ['getActionType'];
        $list['getErrorActionClass'] = ['getErrorActionClass'];
        $list['getMainActionClass'] = ['getMainActionClass'];
        $list['getNotFoundActionClass'] = ['getNotFoundActionClass'];

        return $list;
    }

    /**
     * @test
     * @dataProvider methodsProvider
     */
    public function getters(string $methodName): void
    {
        $this->expectException(NotImplementedException::class);
        $this->expectExceptionMessage(
            'The ConsoleModule module does not have an implementation ' .
            'for this method, as it does not use Actions.'
        );

        $module = $this->makeModule();

        $module->{$methodName}();
    }

    private function makeModule(): ConsoleModule
    {
        return new class extends ConsoleModule
        {
            public function bootDependencies(Container $container): void
            {
            }

            public function getCommandName(): string
            {
                return '';
            }

            public function getCommandPath(): string
            {
                return '';
            }
        };
    }
}
