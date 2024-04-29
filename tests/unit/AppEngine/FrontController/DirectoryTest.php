<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController;

use InvalidArgumentException;
use Iquety\Application\AppEngine\FrontController\Directory;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Tests\Unit\AppEngine\FrontController\Stubs\Commands\OneCommand;
use Tests\Unit\AppEngine\FrontController\Stubs\Commands\SubDirectory\TwoCommand;
use Tests\Unit\TestCase;

class DirectoryTest extends TestCase
{
    /** @test */
    public function objectConstruction(): void
    {
        new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
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
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
            __DIR__ . "/NotExists"
        );
    }

    /** @test */
    public function getIdentity(): void
    {
        $directory = new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
            __DIR__ . "/Stubs"
        );

        $this->assertSame(
            md5('Tests\Unit\AppEngine\FrontController\Stubs\Commands' . __DIR__ . "/Stubs"),
            $directory->getIdentity()
        );
    }

    /** @test */
    public function getDescriptorLevelOne(): void
    {
        $directory = new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
            __DIR__ . "/Stubs"
        );

        $descriptor = $directory->getDescriptorTo(FcBootstrap::class, 'one-command');

        $this->assertSame(OneCommand::class . "::execute", $descriptor->action());
        $this->assertSame([], $descriptor->params());
    }

    /** @test */
    public function getCommandLevelTwo(): void
    {
        $directory = new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
            __DIR__ . "/Stubs"
        );

        $descriptor = $directory->getDescriptorTo(FcBootstrap::class, 'sub-directory/two-command');

        $this->assertSame(TwoCommand::class . "::execute", $descriptor->action());
        $this->assertSame([], $descriptor->params());
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
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
            __DIR__ . "/Stubs"
        );

        $descriptor = $directory->getDescriptorTo(FcBootstrap::class, $uri);

        $this->assertSame($className . "::execute", $descriptor->action());
        $this->assertSame([], $descriptor->params());
    }

    /** 
     * @test 
     * @dataProvider uriProvider
     */
    public function getCommandToSpaces(string $uri, string $className): void
    {
        $directory = new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
            __DIR__ . "/Stubs"
        );

        $descriptor = $directory->getDescriptorTo(FcBootstrap::class, " $uri");
        $this->assertSame($className . "::execute", $descriptor->action());
        $this->assertSame([], $descriptor->params());

        $descriptor = $directory->getDescriptorTo(FcBootstrap::class, " $uri ");
        $this->assertSame($className . "::execute", $descriptor->action());
        $this->assertSame([], $descriptor->params());

        $descriptor = $directory->getDescriptorTo(FcBootstrap::class, "$uri ");
        $this->assertSame($className . "::execute", $descriptor->action());
        $this->assertSame([], $descriptor->params());
    }

    /** @test */
    public function getCommandFixCase(): void
    {
        $directory = new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
            __DIR__ . "/Stubs"
        );

        $descriptor = $directory->getDescriptorTo(FcBootstrap::class, 'sub-diRECtory/two-coMMand');
        $this->assertSame(TwoCommand::class . "::execute", $descriptor->action());
        $this->assertSame([], $descriptor->params());
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidCommandProvider(): array
    {
        $list = [];

        $list['empty']               = [ '' ];
        $list['empty space']         = [ ' ' ];
        $list['one bar']             = [ '/' ];
        $list['one bar left space']  = [ ' /' ];
        $list['one bar right space'] = [ ' /' ];
        $list['one bar both spaces'] = [ ' / ' ];
        $list['nonexistent']         = [ 'not-exists-command' ];

        return $list;
    }

    /** 
     * @test 
     * @dataProvider invalidCommandProvider
     */
    public function getInvalidCommand(string $uri): void
    {
        $directory = new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
            __DIR__ . "/Stubs"
        );

        $this->assertNull($directory->getDescriptorTo(FcBootstrap::class, $uri));
    }

    /** @return array<string,array<int,mixed>> */
    public function commandParamsProvider(): array
    {
        $list = [];

        $list['one: no params']      = [ 'one-command', [] ];

        $list['one: 1 param float']  = [ 'one-command/1.0', [1.0] ];
        $list['one: 1 param int']    = [ 'one-command/11', [11] ];
        $list['one: 1 param string'] = [ 'one-command/identity', ['identity'] ];
        
        $list['one: 2 params float']  = [ 'one-command/1.1/2.2', [1.1, 2.2] ];
        $list['one: 2 params int']    = [ 'one-command/11/22', [11, 22] ];
        $list['one: 2 params string'] = [ 'one-command/one/two', ['one', 'two'] ];

        $list['one: 3 params float']  = [ 'one-command/1.1/2.2/3.3', [1.1, 2.2, 3.3] ];
        $list['one: 3 params int']    = [ 'one-command/11/22/33', [11, 22, 33] ];
        $list['one: 3 params string'] = [ 'one-command/one/two/three', ['one', 'two', 'three'] ];

        $list['two: no params']      = [ 'sub-directory/two-command', [] ];

        $list['two: 1 param float']  = [ 'sub-directory/two-command/1.0', [1.0] ];
        $list['two: 1 param int']    = [ 'sub-directory/two-command/11', [11] ];
        $list['two: 1 param string'] = [ 'sub-directory/two-command/identity', ['identity'] ];
        
        $list['two: 2 params float']  = [ 'sub-directory/two-command/1.1/2.2', [1.1, 2.2] ];
        $list['two: 2 params int']    = [ 'sub-directory/two-command/11/22', [11, 22] ];
        $list['two: 2 params string'] = [ 'sub-directory/two-command/one/two', ['one', 'two'] ];

        $list['two: 3 params float']  = [ 'sub-directory/two-command/1.1/2.2/3.3', [1.1, 2.2, 3.3] ];
        $list['two: 3 params int']    = [ 'sub-directory/two-command/11/22/33', [11, 22, 33] ];
        $list['two: 3 params string'] = [ 'sub-directory/two-command/one/two/three', ['one', 'two', 'three'] ];

        return $list;
    }

    /** 
     * @test 
     * @dataProvider commandParamsProvider
     * @param array<int,string|int|float> $paramList
     */
    public function commandParams(string $uri, array $paramList): void
    {
        $directory = new Directory(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
            __DIR__ . "/Stubs"
        );

        $descriptor = $directory->getDescriptorTo(FcBootstrap::class, $uri);

        $this->assertSame($paramList, $descriptor->params());
    }
}
