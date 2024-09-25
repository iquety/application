<?php

declare(strict_types=1);

namespace Tests\IoEngine;

use Iquety\Application\IoEngine\ActionDescriptor;
use Iquety\Application\IoEngine\FrontController\Command\Command;
use Iquety\Application\IoEngine\FrontController\FcModule;
use Tests\IoEngine\Stubs\FrontController\GetCommand;
use Tests\TestCase;

class ActionDescriptorTest extends TestCase
{
    /** @test */
    public function getters(): void
    {
        $descriptor = new ActionDescriptor(
            Command::class,
            FcModule::class,
            GetCommand::class,
            'execute'
        );

        $this->assertSame(Command::class, $descriptor->type());
        $this->assertSame(FcModule::class, $descriptor->module());
        $this->assertSame(GetCommand::class . '::execute', $descriptor->action());
    }

    /** @test */
    public function getActionClosure(): void
    {
        $closure = fn() => 'kkk';

        $descriptor = new ActionDescriptor(
            Command::class,
            FcModule::class,
            $closure
        );

        $this->assertSame(Command::class, $descriptor->type());
        $this->assertSame(FcModule::class, $descriptor->module());
        $this->assertSame($closure, $descriptor->action());
    }
}
