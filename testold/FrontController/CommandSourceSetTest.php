<?php

declare(strict_types=1);

namespace Tests\IoEngine\FrontController;

use InvalidArgumentException;
use Iquety\Application\IoEngine\FrontController\CommandSourceSet;
use Iquety\Application\IoEngine\FrontController\FcBootstrap;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\ActionDescriptor;
use Iquety\Application\IoEngine\FrontController\CommandSource;
use Tests\IoEngine\FrontController\Stubs\Commands\OneCommand;
use Tests\IoEngine\FrontController\Stubs\Commands\SubDirectory\TwoCommand;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class CommandSourceSetTest extends TestCase
{
    /** @test */
    public function addDirectory(): void
    {
        $directorySet = new CommandSourceSet(FcBootstrap::class);

        $directorySet->add(new CommandSource(
            'Tests\IoEngine\FrontController\Stubs'
        ));

        $this->assertCount(1, $directorySet->toArray());
    }

    /** @test */
    public function addSameDirectory(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The specified source already exists');

        $directorySet = new CommandSourceSet(FcBootstrap::class);

        $directorySet->add(new CommandSource(
            'Tests\IoEngine\FrontController\Stubs\Commands'
        ));

        $directorySet->add(new CommandSource(
            'Tests\IoEngine\FrontController\Stubs\Commands'
        ));
    }

    /** @test */
    public function addTwoDirectories(): void
    {
        $directorySet = new CommandSourceSet(FcBootstrap::class);

        $directorySet->add(new CommandSource(
            'Tests\IoEngine\FrontController\Stubs\Commands'
        ));

        $directorySet->add(new CommandSource(
            'Tests\IoEngine\FrontController\Stubs\Commands\SubDirectory'
        ));

        $this->assertCount(2, $directorySet->toArray());
    }

    /** @test */
    public function getCommandWithoutDirectories(): void
    {
        $directorySet = new CommandSourceSet(FcBootstrap::class);

        $this->assertNull($directorySet->getDescriptorTo(Input::fromString('one-command')));
    }

    /** @test */
    public function getCommandToLevelOne(): void
    {
        $directorySet = new CommandSourceSet(FcBootstrap::class);

        $directorySet->add(new CommandSource(
            'Tests\IoEngine\FrontController\Stubs\Commands'
        ));

        $directorySet->add(new CommandSource(
            'Tests\IoEngine\FrontController\Stubs\Commands\SubDirectory'
        ));

        $descriptor = $directorySet->getDescriptorTo(Input::fromString('one-command'));
        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);
        $this->assertSame(OneCommand::class . "::execute", $descriptor->action());

        $descriptor = $directorySet->getDescriptorTo(Input::fromString('two-command/param/add'));
        $this->assertNotNull($descriptor);
        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);
        $this->assertSame(TwoCommand::class . "::execute", $descriptor->action());
    }

    /** @test */
    public function getCommandToLevelTwo(): void
    {
        $directorySet = new CommandSourceSet(FcBootstrap::class);

        $directorySet->add(new CommandSource(
            'Tests\IoEngine\FrontController\Stubs\Commands'
        ));

        $descriptor = $directorySet->getDescriptorTo(Input::fromString('one-command'));
        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);
        $this->assertSame(OneCommand::class . "::execute", $descriptor->action());

        $descriptor = $directorySet->getDescriptorTo(Input::fromString('sub-directory/two-command'));
        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);
        $this->assertSame(TwoCommand::class . "::execute", $descriptor->action());
    }

    /** @test */
    public function getCommandInexistent(): void
    {
        $directorySet = new CommandSourceSet(FcBootstrap::class);

        $directorySet->add(new CommandSource(
            'Tests\IoEngine\FrontController\Stubs\Commands'
        ));

        $this->assertNull($directorySet->getDescriptorTo(Input::fromString('not-exists')));
    }
}
