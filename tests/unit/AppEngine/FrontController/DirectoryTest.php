<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController;

use InvalidArgumentException;
use Iquety\Application\AppEngine\FrontController\Command;
use Iquety\Application\AppEngine\FrontController\Directory;
use RuntimeException;
use Tests\Unit\AppEngine\FrontController\Stubs\OneCommand;
use Tests\Unit\AppEngine\FrontController\Stubs\SubDirectory\TwoCommand;
use Tests\Unit\TestCase;

class DirectoryTest extends TestCase
{
    /** @test */
    public function objectConstruction(): void
    {
        new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs',
            __DIR__ . "/Stubs"
        );

        $this->assertTrue(true);
    }

    /** @test */
    public function constructionError(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The directory specified for commands does not exist');

        new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs',
            __DIR__ . "/NotExists"
        );
    }

    /** @test */
    public function getIdentity(): void
    {
        $directory = new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs',
            __DIR__ . "/Stubs"
        );

        $this->assertSame(
            md5('Tests\Unit\AppEngine\FrontController\Stubs' . __DIR__ . "/Stubs"),
            $directory->getIdentity()
        );
    }

    /** @test */
    public function getCommandLevelOne(): void
    {
        $directory = new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs',
            __DIR__ . "/Stubs"
        );

        $command = $directory->getCommandTo('one-command');

        $this->assertInstanceOf(Command::class, $command);
    }

    /** @test */
    public function getCommandLevelTwo(): void
    {
        $directory = new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs',
            __DIR__ . "/Stubs"
        );

        $command = $directory->getCommandTo('sub-directory/two-command');

        $this->assertInstanceOf(Command::class, $command);
    }

    /** @return array<string,array<int,mixed>> */
    public function uriProvider(): array
    {
        $list = [];

        $list['one command without bars'] = [ 'one-command', OneCommand::class ];
        $list['two command without bars'] = [ 'sub-directory/two-command', TwoCommand::class ];

        $list['one command with bars'] = [ '/one-command/', OneCommand::class ];
        $list['two command with bars'] = [ '/sub-directory/two-command/', TwoCommand::class ];

        $list['one command with start bar'] = [ '/one-command', OneCommand::class ];
        $list['two command with start bar'] = [ '/sub-directory/two-command', TwoCommand::class ];

        $list['one command with finish bar'] = [ 'one-command/', OneCommand::class ];
        $list['two command with finish bar'] = [ 'sub-directory/two-command/', TwoCommand::class ];

        return $list;
    }

    /** 
     * @test 
     * @dataProvider uriProvider
     */
    public function getCommandToBars(string $uri, string $className): void
    {
        $directory = new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs',
            __DIR__ . "/Stubs"
        );

        $this->assertInstanceOf($className, $directory->getCommandTo($uri));
    }

    /** 
     * @test 
     * @dataProvider uriProvider
     */
    public function getCommandToSpaces(string $uri, string $className): void
    {
        $directory = new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs',
            __DIR__ . "/Stubs"
        );

        $this->assertInstanceOf($className, $directory->getCommandTo(" $uri"));
        $this->assertInstanceOf($className, $directory->getCommandTo("$uri "));
        $this->assertInstanceOf($className, $directory->getCommandTo(" $uri "));
    }

    /** @test */
    public function getCommandFixCase(): void
    {
        $directory = new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs',
            __DIR__ . "/Stubs"
        );

        $command = $directory->getCommandTo('sub-diRECtory/two-coMMand');

        $this->assertInstanceOf(Command::class, $command);

        $this->assertSame(
            'Tests\Unit\AppEngine\FrontController\Stubs\SubDirectory\TwoCommand',
            
            $directory->getLastClassName()
        );
        
        $this->assertSame(
            'sub-diRECtory/two-coMMand',
            $directory->getLastUri()
        );
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidCommandProvider(): array
    {
        $list = [];

        $list['nonexistent'] = [
            'not-exists-command',
            'Tests\Unit\AppEngine\FrontController\Stubs\NotExistsCommand'
        ];

        // $list['invalid case'] = [
        //     'sub-direcTory/two-coMmand',
        //     'Tests\Unit\AppEngine\FrontController\Stubs\SubDirectory/twoComand'
        // ];

        return $list;
    }

    /** 
     * @test 
     * @dataProvider invalidCommandProvider
     */
    public function getInvalidCommand(string $uri, string $command): void
    {
        $directory = new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs',
            __DIR__ . "/Stubs"
        );

        $this->assertNull($directory->getCommandTo($uri));
    }
}
