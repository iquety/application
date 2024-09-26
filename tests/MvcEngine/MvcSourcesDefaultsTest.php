<?php

declare(strict_types=1);

namespace Tests\MvcEngine;

use Iquety\Application\IoEngine\ActionDescriptor;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;
use Iquety\Application\IoEngine\Mvc\Controller\ErrorController;
use Iquety\Application\IoEngine\Mvc\Controller\MainController;
use Iquety\Application\IoEngine\Mvc\Controller\NotFoundController;
use Iquety\Application\IoEngine\Mvc\MvcSourceHandler;
use Tests\TestCase;

class MvcSourcesDefaultsTest extends TestCase
{
    /** @test */
    public function errorDescriptor(): void
    {
        $handler = new MvcSourceHandler();

        $descriptor = $handler->getErrorDescriptor();

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        $this->assertSame('error', $descriptor->module());
        $this->assertSame(Controller::class, $descriptor->type());
        $this->assertSame(ErrorController::class . '::execute', $descriptor->action());
    }

    /** @test */
    public function mainDescriptor(): void
    {
        $handler = new MvcSourceHandler();

        $descriptor = $handler->getMainDescriptor();

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        $this->assertSame('main', $descriptor->module());
        $this->assertSame(Controller::class, $descriptor->type());
        $this->assertSame(MainController::class . '::execute', $descriptor->action());
    }

    /** @test */
    public function notFoundDescriptor(): void
    {
        $handler = new MvcSourceHandler();

        $descriptor = $handler->getNotFoundDescriptor();

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        $this->assertSame('not-found', $descriptor->module());
        $this->assertSame(Controller::class, $descriptor->type());
        $this->assertSame(NotFoundController::class . '::execute', $descriptor->action());
    }
}
