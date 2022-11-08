<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\CommandHandler;
use Tests\AppEngine\FrontController\Support\FcBootstrapConcrete;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class HandlerResolveCommandTest extends TestCase
{
    /** @test */
    public function processUnresolved(): void
    {
        $handler = new CommandHandler();

        $handler->addNamespace(
            FcBootstrapConcrete::class,
            $this->extractNamespace(FcBootstrapConcrete::class, 'CommandsDir')
        );

        $possibilityList = $handler->process('invalid');

        $this->assertNull($handler->resolveCommand($possibilityList));
    }

    public function commandsProvider(): array
    {
        $list = [];

        $list[] = [ 'one', 'CommandsDir\One::execute', [] ];
        $list[] = [ 'one/two', 'CommandsDir\OneTwo::execute', [] ];

        $list[] = [ 'one', 'CommandsDir\One::execute', [] ];
        $list[] = [ 'one/two', 'CommandsDir\OneTwo::execute', [] ];
        $list[] = [ 'one/two/three', 'CommandsDir\OneTwoThree::execute', [] ];

        $list[] = [ 'alpha/beta', 'CommandsDir\Alpha\Beta::execute', [] ];
        $list[] = [ 'alpha/beta/gamma', 'CommandsDir\Alpha\BetaGamma::execute', [] ];
        $list[] = [ 'alpha/beta/gamma/delta', 'CommandsDir\Alpha\BetaGammaDelta::execute', [] ];

        $list[] = [ 'one/43', 'CommandsDir\One::execute', [43] ];
        $list[] = [ 'one/two/43', 'CommandsDir\OneTwo::execute', [43] ];

        $list[] = [ 'one/val/43', 'CommandsDir\One::execute', ['val', 43] ];
        $list[] = [ 'one/two/val/43', 'CommandsDir\OneTwo::execute', ['val', 43] ];

        $list[] = [ 'one/val/val', 'CommandsDir\One::execute', ['val', 'val'] ];
        $list[] = [ 'one/two/val/val', 'CommandsDir\OneTwo::execute', ['val', 'val'] ];

        $list[] = [ 'one/43/43', 'CommandsDir\One::execute', [43, 43] ];
        $list[] = [ 'one/two/43/43', 'CommandsDir\OneTwo::execute', [43, 43] ];

        return $list;
    }

    /**
     * @test
     * @dataProvider commandsProvider
     */
    public function processCommand(string $path, string $commandSufix, array $params): void
    {
        $handler = new CommandHandler();

        $handler->addNamespace(
            FcBootstrapConcrete::class,
            $this->extractNamespace(FcBootstrapConcrete::class, 'CommandsDir')
        );

        $possibilityList = $handler->process($path);

        $descriptor = $handler->resolveCommand($possibilityList);

        $this->assertSame(FcBootstrapConcrete::class, $descriptor->module());

        $this->assertSame(
            $this->extractNamespace(FcBootstrapConcrete::class, $commandSufix),
            $descriptor->action()
        );

        $this->assertSame($params, $descriptor->params());
    }
}
