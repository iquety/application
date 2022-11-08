<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\CommandHandler;
use Tests\AppEngine\FrontController\Support\FcBootstrapAlterDir;
use Tests\AppEngine\FrontController\Support\FcBootstrapConcrete;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class HandlerNamespaceTest extends TestCase
{
    /** @test */
    public function namespaces(): void
    {
        $handler = new CommandHandler();

        $handler->addNamespace(
            FcBootstrapConcrete::class,
            $this->extractNamespace(FcBootstrapConcrete::class, 'Commands')
        );
        
        $this->assertCount(1, $handler->namespaces());
        $this->assertEquals([
            FcBootstrapConcrete::class => $this->extractNamespace(FcBootstrapConcrete::class, 'Commands'),
        ], $handler->namespaces());

        $handler->addNamespace(
            FcBootstrapAlterDir::class,
            $this->extractNamespace(FcBootstrapAlterDir::class, 'Commands')
        );

        $this->assertCount(2, $handler->namespaces());
        $this->assertEquals([
            FcBootstrapConcrete::class => $this->extractNamespace(FcBootstrapConcrete::class, 'Commands'),
            FcBootstrapAlterDir::class => $this->extractNamespace(FcBootstrapAlterDir::class, 'Commands'),
        ], $handler->namespaces());
    }
}
