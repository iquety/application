<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\CommandHandler;
use Iquety\Application\AppEngine\FrontController\CommandPossibility;
use Tests\AppEngine\FrontController\Support\FcBootstrapConcrete;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class HandlerPreparationTest extends TestCase
{
    /** @test */
    public function withoutNamespaces(): void
    {
        $handler = new CommandHandler();

        $this->assertEquals([], $handler->process(''));
    }

    public function sanitizeEmptyPathProvider(): array
    {
        return [
            ['?id=ok'],
            ['#teste'],
            ['/'],

            ['/?id=ok'],
            ['/#teste'],
            ['/#teste'],

            ['//'],
            ['//?id=ok'],
            ['//#teste'],
            ['//#teste'],

            ['///'],
            ['/////////'],
        ];
    }

    /**
     * @test
     * @dataProvider sanitizeEmptyPathProvider
     */
    public function sanitizeEmptyPath(string $path): void
    {
        $handler = new CommandHandler();

        $handler->addNamespace(
            FcBootstrapConcrete::class,
            $this->extractNamespace(FcBootstrapConcrete::class, 'CommandsDir')
        );

        $this->assertEquals([], $handler->process($path));
    }

    public function sanitizePathProvider(): array
    {
        return [
            ['//add//'],
            ['//add//?id=ok'],
            ['//add//#teste'],
            ['//add//#teste'],

            ['/add//'],
            ['/add//?id=ok'],
            ['/add//#teste'],
            ['/add//#teste'],

            ['//add/'],
            ['//add/?id=ok'],
            ['//add/#teste'],
            ['//add/#teste'],

            ['//add//////'],
            ['//add//////?id=ok'],
            ['//add//////#teste'],
            ['//add//////#teste'],
        ];
    }

    /**
     * @test
     * @dataProvider sanitizePathProvider
     */
    public function sanitizePath(string $path): void
    {
        $handler = new CommandHandler();

        $handler->addNamespace(
            FcBootstrapConcrete::class,
            $this->extractNamespace(FcBootstrapConcrete::class, 'CommandsDir')
        );

        $this->assertEquals(new CommandPossibility(
            FcBootstrapConcrete::class,
            $this->extractNamespace(FcBootstrapConcrete::class, 'CommandsDir\Add'),
            []
        ), $handler->process($path)[0]);

        $this->assertEquals(['add'], $this->getPropertyValue($handler, 'pathNodes'));
    }
}
