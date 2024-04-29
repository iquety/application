<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController;

use InvalidArgumentException;
use Iquety\Application\AppEngine\FrontController\Directory;
use Iquety\Application\AppEngine\FrontController\DirectorySet;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Tests\Unit\AppEngine\FrontController\Stubs\Commands\OneCommand;
use Tests\Unit\AppEngine\FrontController\Stubs\Commands\SubDirectory\TwoCommand;
use Tests\Unit\TestCase;

class DirectorySetTest extends TestCase
{
    /** @test */
    public function addDirectory(): void
    {
        $directorySet = new DirectorySet(FcBootstrap::class);

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs',
            __DIR__ . "/Stubs"
        ));

        $this->assertCount(1, $directorySet->toArray());
    }

    /** @test */
    public function addSameDirectory(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The specified directory already exists');

        $directorySet = new DirectorySet(FcBootstrap::class);

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
            __DIR__ . "/Stubs"
        ));

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
            __DIR__ . "/Stubs"
        ));
    }

    /** @test */
    public function addTwoDirectories(): void
    {
        $directorySet = new DirectorySet(FcBootstrap::class);

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
            __DIR__ . "/Stubs/Commands"
        ));

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands\SubDirectory',
            __DIR__ . "/Stubs/Commands/SubDirectory"
        ));

        $this->assertCount(2, $directorySet->toArray());
    }

    /** @test */
    public function getCommandWithoutDirectories(): void
    {
        $directorySet = new DirectorySet(FcBootstrap::class);

        $this->assertNull($directorySet->getDescriptorTo('one-command'));
    }

    /** @test */
    public function getCommandToLevelOne(): void
    {
        $directorySet = new DirectorySet(FcBootstrap::class);

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
            __DIR__ . "/Stubs/Commands"
        ));

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands\SubDirectory',
            __DIR__ . "/Stubs/Commands/SubDirectory"
        ));

        $descriptor = $directorySet->getDescriptorTo('one-command');
        $this->assertSame(OneCommand::class . "::execute", $descriptor->action());
        $this->assertSame([], $descriptor->params());

        $descriptor = $directorySet->getDescriptorTo('two-command');
        $this->assertSame(TwoCommand::class . "::execute", $descriptor->action());
        $this->assertSame([], $descriptor->params());
    }

    /** @test */
    public function getCommandToLevelTwo(): void
    {
        $directorySet = new DirectorySet(FcBootstrap::class);

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
            __DIR__ . "/Stubs/Commands"
        ));

        $descriptor = $directorySet->getDescriptorTo('one-command');
        $this->assertSame(OneCommand::class . "::execute", $descriptor->action());
        $this->assertSame([], $descriptor->params());

        $descriptor = $directorySet->getDescriptorTo('sub-directory/two-command');
        $this->assertSame(TwoCommand::class . "::execute", $descriptor->action());
        $this->assertSame([], $descriptor->params());
    }

    /** @test */
    public function getCommandInexistent(): void
    {
        $directorySet = new DirectorySet(FcBootstrap::class);

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
            __DIR__ . "/Stubs/Commands"
        ));

        $this->assertNull($directorySet->getDescriptorTo('not-exists'));
    }
}
