<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\Source;
use Iquety\Application\AppEngine\Input;
use Tests\Unit\AppEngine\FrontController\Stubs\Commands\OneCommand;
use Tests\Unit\AppEngine\FrontController\Stubs\Commands\SubDirectory\TwoCommand;
use Tests\Unit\TestCase;

class SourceTest extends TestCase
{
    /** @test */
    public function objectConstruction(): void
    {
        new Source(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands'
        );

        $this->assertTrue(true);
    }

    /** @test */
    public function getIdentity(): void
    {
        $directory = new Source(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands'
        );

        $this->assertSame(
            md5('Tests\Unit\AppEngine\FrontController\Stubs\Commands'),
            $directory->getIdentity()
        );
    }

    /** @test */
    public function getDescriptorLevelOne(): void
    {
        $directory = new Source(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands'
        );

        $descriptor = $directory->getDescriptorTo(FcBootstrap::class, Input::fromString('one-command'));

        $this->assertSame(OneCommand::class . "::execute", $descriptor->action());
    }

    /** @test */
    public function getCommandLevelTwo(): void
    {
        $directory = new Source(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands'
        );

        $descriptor = $directory->getDescriptorTo(
            FcBootstrap::class,
            Input::fromString('sub-directory/two-command')
        );

        $this->assertSame(TwoCommand::class . "::execute", $descriptor->action());
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
        $directory = new Source(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands'
        );

        $descriptor = $directory->getDescriptorTo(FcBootstrap::class, Input::fromString($uri));

        $this->assertSame($className . "::execute", $descriptor->action());
    }

    /**
     * @test
     * @dataProvider uriProvider
     */
    public function getCommandToBarsNumeric(string $uri, string $className): void
    {
        $directory = new Source(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands'
        );

        $uri = rtrim($uri, '/') . '/22';

        $descriptor = $directory->getDescriptorTo(FcBootstrap::class, Input::fromString($uri));

        $this->assertSame($className . "::execute", $descriptor->action());
    }

    /**
     * @test
     * @dataProvider uriProvider
     */
    public function getCommandToSpaces(string $uri, string $className): void
    {
        $directory = new Source(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands'
        );

        $descriptor = $directory->getDescriptorTo(FcBootstrap::class, Input::fromString(" $uri"));
        $this->assertSame($className . "::execute", $descriptor->action());

        $descriptor = $directory->getDescriptorTo(FcBootstrap::class, Input::fromString(" $uri "));
        $this->assertSame($className . "::execute", $descriptor->action());

        $descriptor = $directory->getDescriptorTo(FcBootstrap::class, Input::fromString("$uri "));
        $this->assertSame($className . "::execute", $descriptor->action());
    }

    /** @test */
    public function getCommandFixCase(): void
    {
        $directory = new Source(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands'
        );

        $descriptor = $directory->getDescriptorTo(
            FcBootstrap::class,
            Input::fromString('sub-diRECtory/two-coMMand')
        );

        $this->assertSame(TwoCommand::class . "::execute", $descriptor->action());
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidCommandProvider(): array
    {
        $list = [];

        $list['empty']       = [ '' ];
        $list['one bar']     = [ '/' ];
        $list['nonexistent'] = [ 'not-exists-command' ];

        return $list;
    }

    /**
     * @test
     * @dataProvider invalidCommandProvider
     */
    public function getInvalidCommand(string $uri): void
    {
        $directory = new Source(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
        );

        $this->assertNull($directory->getDescriptorTo(
            FcBootstrap::class,
            Input::fromString($uri)
        ));
    }

    public function notExists(): void
    {
        $directory = new Source(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
        );

        $this->assertNull($directory->getDescriptorTo(
            FcBootstrap::class,
            Input::fromString('xxxxx')
        ));
    }
}
