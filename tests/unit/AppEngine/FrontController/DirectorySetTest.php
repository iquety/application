<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController;

use InvalidArgumentException;
use Iquety\Application\AppEngine\FrontController\Command;
use Iquety\Application\AppEngine\FrontController\Directory;
use Iquety\Application\AppEngine\FrontController\DirectorySet;
use Tests\Unit\AppEngine\FrontController\Stubs\OneCommand;
use Tests\Unit\AppEngine\FrontController\Stubs\SubDirectory\TwoCommand;
use Tests\Unit\TestCase;

class DirectorySetTest extends TestCase
{
    /** @test */
    public function addDirectory(): void
    {
        $directorySet = new DirectorySet();

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

        $directorySet = new DirectorySet();

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs',
            __DIR__ . "/Stubs"
        ));

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs',
            __DIR__ . "/Stubs"
        ));
    }

    /** @test */
    public function addTwoDirectories(): void
    {
        $directorySet = new DirectorySet();

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs',
            __DIR__ . "/Stubs"
        ));

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\SubDirectory',
            __DIR__ . "/Stubs/SubDirectory"
        ));

        $this->assertCount(2, $directorySet->toArray());
    }

    /** @test */
    public function getCommandWithoutDirectories(): void
    {
        $directorySet = new DirectorySet();

        $this->assertNull($directorySet->getCommandTo('one-command'));
    }

    /** @test */
    public function getCommandToLevelOne(): void
    {
        $directorySet = new DirectorySet();

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs',
            __DIR__ . "/Stubs"
        ));

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\SubDirectory',
            __DIR__ . "/Stubs/SubDirectory"
        ));

        $this->assertInstanceOf(OneCommand::class, $directorySet->getCommandTo('one-command'));
        $this->assertInstanceOf(TwoCommand::class, $directorySet->getCommandTo('two-command'));
    }

    /** @test */
    public function getCommandToLevelTwo(): void
    {
        $directorySet = new DirectorySet();

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs',
            __DIR__ . "/Stubs"
        ));

        $this->assertInstanceOf(OneCommand::class, $directorySet->getCommandTo('one-command'));
        $this->assertInstanceOf(TwoCommand::class, $directorySet->getCommandTo('sub-directory/two-command'));
    }

    /** @test */
    public function getCommandInexistent(): void
    {
        $directorySet = new DirectorySet();

        $directorySet->add(new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs',
            __DIR__ . "/Stubs"
        ));

        $this->assertNull($directorySet->getCommandTo('not-exists'));
    }
}
