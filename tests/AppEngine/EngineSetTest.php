<?php

declare(strict_types=1);

namespace Tests\AppEngine;

use InvalidArgumentException;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\EngineSet;
use Iquety\Application\AppEngine\FrontController\Command\NotFoundCommand;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\FrontController\CommandSource;
use Iquety\Application\AppEngine\FrontController\CommandSourceSet;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Injection\Container;
use RuntimeException;
use Tests\AppEngine\FrontController\Stubs\Commands\SubDirectory\TwoCommand;
use Tests\TestCase;

class EngineSetTest extends TestCase
{
    /** @test */
    public function duplicatedEngine(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'To return the handler, you must add at least one engine'
        );

        $container = new Container();

        $engineSet = new EngineSet($container);

        $engineSet->sourceHandler();
    }
}
