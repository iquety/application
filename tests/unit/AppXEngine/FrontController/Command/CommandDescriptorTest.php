<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController\Command;

use InvalidArgumentException;
use Iquety\Application\AppEngine\FrontController\Command\Command;
use Iquety\Application\AppEngine\FrontController\Command\CommandDescriptor;
use Iquety\Application\AppEngine\FrontController\Directory;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Tests\Unit\AppEngine\FrontController\Stubs\Commands\OneCommand;
use Tests\Unit\AppEngine\FrontController\Stubs\Commands\SubDirectory\TwoCommand;
use Tests\Unit\TestCase;

class CommandDescriptorTest extends TestCase
{
    /** @test */
    public function objectConstruction(): void
    {
        $descriptor = new CommandDescriptor(
            FcBootstrap::class,
            Command::class,
            []
        );

        $this->assertSame(FcBootstrap::class, $descriptor->module());
        $this->assertSame(Command::class . '::execute', $descriptor->action());
        $this->assertSame([], $descriptor->params());
    }
}
