<?php

declare(strict_types=1);

namespace Tests\IoEngine\Action;

use Iquety\Application\IoEngine\Action\Input;
use RuntimeException;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @see https://github.com/iquety/console/blob/main/docs/pt-br/03-instanciando-o-terminal.md
 * @see https://github.com/iquety/console/blob/main/docs/pt-br/06-usando-os-argumentos.md
 */
class InputConsoleArgumentsTest extends TestCase
{
    /** @return array<string,array<int,mixed>> */
    public function emptyArgumentsProvider(): array
    {
        $list = [];

        $list['empty'] = [
            ['']
        ];

        $list['one space'] = [
            [' ']
        ];

        $list['any spaces'] = [
            ['   ']
        ];

        return $list;
    }

    /**
     * @test
     * @dataProvider emptyArgumentsProvider
     * @param array<int,string> $argumentList
     */
    public function fromCorruptedArguments(array $argumentList): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'The argument list is corrupt. It does not contain the script name'
        );

        Input::fromConsoleArguments($argumentList);
    }

    /** @return array<string,array<int,mixed>> */
    public function inputedArgumentList(): array
    {
        $list = [];

        $list['only script'] = [
            ['my-script']
        ];

        $list['with one argument'] = [
            ['my-script', '-a', 'value-to-a']
        ];

        $list['with any arguments'] = [
            [
                'my-script',
                '-a',
                'value-to-a',
                '-b',
                '-c',
                'value-to-c'
            ]
        ];

        $list['with isolated arguments'] = [
            [
                'my-script',
                '-a',
                'value-to-a',
                '-b',
                '-c',
                'value-to-c',
                'isolated-one',
                'isolated-two'
            ]
        ];

        return $list;
    }

    /**
     * @test
     * @dataProvider inputedArgumentList
     * @param array<int,string> $argumentList Conteúdo de $argv
     * @see https://www.php.net/manual/pt_BR/reserved.variables.argv.php
     */
    public function fromArrayArgv(array $argumentList): void
    {
        $input = Input::fromConsoleArguments($argumentList);

        $this->assertSame('', $input->getPathString());
        $this->assertSame([], $input->getPath());
        $this->assertSame('CLI', $input->getMethod());
        $this->assertSame($argumentList, $input->toArray());
    }

    /** @test */
    public function next(): void
    {
        $input = Input::fromConsoleArguments([
            'my-script',
            '-x',
            'four',
            '-y',
            'five',
            '-z',
            'six'
        ]);

        $this->assertSame('CLI', $input->getMethod());
        $this->assertSame('', $input->getPathString());
        $this->assertSame([], $input->getPath());

        $this->assertSame([], $input->getTarget());
        $this->assertSame('my-script', $input->param(0));
        $this->assertSame('-x', $input->param(1));

         // o próximo nó do 'path' nunca vai existir no terminal
        $this->assertFalse($input->hasNext());

        $this->assertSame([
            0 => 'my-script',
            1 => '-x',
            2 => 'four',
            3 => '-y',
            4 => 'five',
            5 => '-z',
            6 => 'six'
        ], $input->toArray());

        // - - - - - - - - - - - - - - - - - - - - - - -

        $input->next(); // o próximo nó do 'path' nunca vai existir no terminal


        $this->assertSame('CLI', $input->getMethod());
        $this->assertSame('', $input->getPathString());
        $this->assertSame([], $input->getPath());

        $this->assertSame([], $input->getTarget());
        $this->assertSame('my-script', $input->param(0));
        $this->assertSame('-x', $input->param(1));

         // o próximo nó do 'path' nunca vai existir no terminal
        $this->assertFalse($input->hasNext());

        $this->assertSame([
            0 => 'my-script',
            1 => '-x',
            2 => 'four',
            3 => '-y',
            4 => 'five',
            5 => '-z',
            6 => 'six'
        ], $input->toArray());
    }

    /** @test */
    public function reset(): void
    {
        $input = Input::fromConsoleArguments([
            'my-script',
            '-x',
            'four',
            '-y',
            'five',
            '-z',
            'six'
        ]);

        $this->assertSame('CLI', $input->getMethod());
        $this->assertSame('', $input->getPathString());
        $this->assertSame([], $input->getPath());

        $this->assertSame([], $input->getTarget());
        $this->assertSame('my-script', $input->param(0));
        $this->assertSame('-x', $input->param(1));

         // o próximo nó do 'path' nunca vai existir no terminal
        $this->assertFalse($input->hasNext());

        $this->assertSame([
            0 => 'my-script',
            1 => '-x',
            2 => 'four',
            3 => '-y',
            4 => 'five',
            5 => '-z',
            6 => 'six'
        ], $input->toArray());

        // - - - - - - - - - - - - - - - - - - - - - - -

        $input->next(); // o próximo nó do 'path' nunca vai existir no terminal
        $input->next();

        $this->assertSame('CLI', $input->getMethod());
        $this->assertSame('', $input->getPathString());
        $this->assertSame([], $input->getPath());

        $this->assertSame([], $input->getTarget());
        $this->assertSame('my-script', $input->param(0));
        $this->assertSame('-x', $input->param(1));

         // o próximo nó do 'path' nunca vai existir no terminal
        $this->assertFalse($input->hasNext());

        $this->assertSame([
            0 => 'my-script',
            1 => '-x',
            2 => 'four',
            3 => '-y',
            4 => 'five',
            5 => '-z',
            6 => 'six'
        ], $input->toArray());

        $input->reset();

        $this->assertSame('CLI', $input->getMethod());
        $this->assertSame('', $input->getPathString());
        $this->assertSame([], $input->getPath());

        $this->assertSame([], $input->getTarget());
        $this->assertSame('my-script', $input->param(0));
        $this->assertSame('-x', $input->param(1));

         // o próximo nó do 'path' nunca vai existir no terminal
        $this->assertFalse($input->hasNext());

        $this->assertSame([
            0 => 'my-script',
            1 => '-x',
            2 => 'four',
            3 => '-y',
            4 => 'five',
            5 => '-z',
            6 => 'six'
        ], $input->toArray());
    }

    /** @test */
    public function getParam(): void
    {
        $input = Input::fromConsoleArguments([
            'my-script',
            '-x',
            'four',
            '-y',
            'five',
            '-z',
            'six'
        ]);

        $this->assertSame('CLI', $input->getMethod());
        $this->assertSame('', $input->getPathString());
        $this->assertSame([], $input->getPath());

        $this->assertSame([], $input->getTarget());
        $this->assertSame('my-script', $input->param(0));
        $this->assertSame('-x', $input->param(1));

         // o próximo nó do 'path' nunca vai existir no terminal
        $this->assertFalse($input->hasNext());

        $this->assertSame([
            0 => 'my-script',
            1 => '-x',
            2 => 'four',
            3 => '-y',
            4 => 'five',
            5 => '-z',
            6 => 'six'
        ], $input->toArray());

        $this->assertSame('my-script', $input->param(0));
        $this->assertSame('-x', $input->param(1));
        $this->assertSame('four', $input->param(2));
        $this->assertSame('-y', $input->param(3));
        $this->assertSame('five', $input->param(4));
        $this->assertSame('-z', $input->param(5));
        $this->assertSame('six', $input->param(6));

        $input->next();

        $this->assertSame([
            0 => 'my-script',
            1 => '-x',
            2 => 'four',
            3 => '-y',
            4 => 'five',
            5 => '-z',
            6 => 'six'
        ], $input->toArray());

        $this->assertSame('my-script', $input->param(0));
        $this->assertSame('-x', $input->param(1));
        $this->assertSame('four', $input->param(2));
        $this->assertSame('-y', $input->param(3));
        $this->assertSame('five', $input->param(4));
        $this->assertSame('-z', $input->param(5));
        $this->assertSame('six', $input->param(6));
    }

    /** @test */
    public function inputToString(): void
    {
        $input = Input::fromConsoleArguments([
            'my-script',
            '-x',
            'four',
            '-y',
            'five',
            '-z',
            'six'
        ]);

        $this->assertSame('CLI', $input->getMethod());
        $this->assertSame('', $input->getPathString());
        $this->assertSame([], $input->getTarget());

        $this->assertSame([
            0 => 'my-script',
            1 => '-x',
            2 => 'four',
            3 => '-y',
            4 => 'five',
            5 => '-z',
            6 => 'six'
        ], $input->toArray());

        $this->assertSame(
            '0=my-script&1=-x&2=four&3=-y&4=five&5=-z&6=six',
            (string)$input
        );
    }

    /** @test */
    public function appendParams(): void
    {
        $input = Input::fromConsoleArguments([
            'my-script',
            '-x',
            'four',
            '-y',
            'five',
            '-z',
            'six'
        ]);

        $this->assertSame('CLI', $input->getMethod());
        $this->assertSame('', $input->getPathString());
        $this->assertSame([], $input->getTarget());

        $this->assertSame([
            0 => 'my-script',
            1 => '-x',
            2 => 'four',
            3 => '-y',
            4 => 'five',
            5 => '-z',
            6 => 'six'
        ], $input->toArray());

        $this->assertSame(
            '0=my-script&1=-x&2=four&3=-y&4=five&5=-z&6=six',
            (string)$input
        );

        $input->appendParams(['id' => 99, 'name' => 'Teste']);

        $this->assertSame([
            0 => 'my-script',
            1 => '-x',
            2 => 'four',
            3 => '-y',
            4 => 'five',
            5 => '-z',
            6 => 'six',
            'id' => 99,
            'name' => 'Teste'
        ], $input->toArray());

        $this->assertSame(
            '0=my-script&1=-x&2=four&3=-y&4=five&5=-z&6=six&id=99&name=Teste',
            (string)$input
        );
    }
}
