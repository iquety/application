<?php

declare(strict_types=1);

namespace Tests\FcEngine;

use Iquety\Application\IoEngine\ActionDescriptor;
use Iquety\Application\IoEngine\FrontController\Command\Command;
use Iquety\Application\IoEngine\FrontController\Command\ErrorCommand;
use Iquety\Application\IoEngine\FrontController\Command\MainCommand;
use Iquety\Application\IoEngine\FrontController\Command\NotFoundCommand;
use Iquety\Application\IoEngine\FrontController\FcSourceHandler;
use Tests\TestCase;

class FcSourcesDefaultsTest extends TestCase
{
    /** @test */
    public function errorDescriptor(): void
    {
        $handler = new FcSourceHandler();

        $descriptor = $handler->getErrorDescriptor();

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        $this->assertSame('error', $descriptor->module());
        $this->assertSame(Command::class, $descriptor->type());
        $this->assertSame(ErrorCommand::class . '::execute', $descriptor->action());
    }

    /** @test */
    public function mainDescriptor(): void
    {
        $handler = new FcSourceHandler();

        $descriptor = $handler->getMainDescriptor();

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        $this->assertSame('main', $descriptor->module());
        $this->assertSame(Command::class, $descriptor->type());
        $this->assertSame(MainCommand::class . '::execute', $descriptor->action());
    }

    /** @test */
    public function notFoundDescriptor(): void
    {
        $handler = new FcSourceHandler();

        $descriptor = $handler->getNotFoundDescriptor();

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        $this->assertSame('not-found', $descriptor->module());
        $this->assertSame(Command::class, $descriptor->type());
        $this->assertSame(NotFoundCommand::class . '::execute', $descriptor->action());
    }
}
