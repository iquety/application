<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\CommandHandler;
use Tests\AppEngine\FrontController\Support\CommandsDir\OneTwoThree;
use Tests\AppEngine\FrontController\Support\FcBootstrapConcrete;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class HandlerResolveMainTest extends TestCase
{
    /** @test */
    public function processMainUnresolved(): void
    {
        $handler = new CommandHandler();
        
        $handler->addNamespace(
            FcBootstrapConcrete::class,
            $this->extractNamespace(FcBootstrapConcrete::class, 'CommandsDir')
        );

        $possibilityList = $handler->process('');

        $this->assertEquals([], $possibilityList);
        $this->assertNull($handler->resolveCommand($possibilityList));
    }

    public function mainPathProvider(): array
    {
        return [
            'empty' => ['', []],
            'query' => ['?id=33', []],
            'bar' => ['/', []],
            'bar + query' => ['/?id=33', []],
        ];
    }

    /** 
     * @test
     * @dataProvider mainPathProvider
     */
    public function processMainCommand(string $path, array $params): void
    {
        $handler = new CommandHandler();
        
        $handler->addNamespace(
            FcBootstrapConcrete::class,
            $this->extractNamespace(FcBootstrapConcrete::class, 'CommandsDir')
        );

        $handler->setRootCommand(OneTwoThree::class);

        $possibilityList = $handler->process($path);

        $descriptor = $handler->resolveCommand($possibilityList);

        $this->assertSame('', $descriptor->module());

        $this->assertSame(
            $this->extractNamespace(FcBootstrapConcrete::class, 'CommandsDir\OneTwoThree::execute'),
            $descriptor->action()
        );

        $this->assertSame($params, $descriptor->params());
    }

    public function invalideMainPathProvider(): array
    {
        return [
            'value' => ['43'],
            'value + query' => ['43?id=33'],
            'bar + value' => ['/43'],
            'bar + value + query' => ['/43?id=33'],
        ];
    }

    /** 
     * @test
     * @dataProvider invalideMainPathProvider
     */
    public function processInvalidMainCommand(string $path): void
    {
        $handler = new CommandHandler();
        
        $handler->addNamespace(
            FcBootstrapConcrete::class,
            $this->extractNamespace(FcBootstrapConcrete::class, 'CommandsDir')
        );

        $handler->setRootCommand(OneTwoThree::class);

        $possibilityList = $handler->process($path);

        $this->assertNull($handler->resolveCommand($possibilityList));
    }
}
