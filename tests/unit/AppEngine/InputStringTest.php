<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine;

use Iquety\Application\AppEngine\Action\Input;
use Tests\Unit\TestCase;

class InputStringTest extends TestCase
{
    /** @test */
    public function fromString(): void
    {
        $input = Input::fromString('/one/two/03?x=four&y=five&z=six');

        $this->assertSame('GET', $input->getMethod());
        $this->assertSame('one/two/03', $input->getPathString());
        $this->assertSame(['one', 'two', '03'], $input->getPath());
        $this->assertSame(['one'], $input->getTarget());
        $this->assertSame('two', $input->param(0));
        $this->assertSame(3, $input->param(1));

        $this->assertTrue($input->hasNext()); // two é o próximo

        $this->assertSame([
            0 => 'two',
            1 => 3,
            'x' => 'four',
            'y' => 'five',
            'z' => 'six'
        ], $input->toArray());
    }

    /** @test */
    public function fromEmptyPath(): void
    {
        $input = Input::fromString('?x=four&y=five&z=six');

        $this->assertSame('GET', $input->getMethod());
        $this->assertSame('', $input->getPathString());
        $this->assertSame([], $input->getPath());
        $this->assertSame([], $input->getTarget());
        $this->assertNull($input->param(0));
        $this->assertFalse($input->hasNext()); // não há path, não há próximo

        $this->assertSame([
            'x' => 'four',
            'y' => 'five',
            'z' => 'six'
        ], $input->toArray());
    }

    /** @test */
    public function next(): void
    {
        $input = Input::fromString('/one/two/03?x=four&y=five&z=six');

        $this->assertSame('GET', $input->getMethod());
        $this->assertSame('one/two/03', $input->getPathString());
        $this->assertSame(['one', 'two', '03'], $input->getPath());
        $this->assertSame(['one'], $input->getTarget());
        $this->assertSame('two', $input->param(0));
        $this->assertSame(3, $input->param(1));
        $this->assertTrue($input->hasNext()); // two é o próximo
        $this->assertSame([
            0 => 'two',
            1 => 3,
            'x' => 'four',
            'y' => 'five',
            'z' => 'six'
        ], $input->toArray());

        // - - - - - - - - - - - - - - - - - - - - - - -

        $input->next(); // adiciona two

        $this->assertSame('GET', $input->getMethod());
        $this->assertSame('one/two/03', $input->getPathString());
        $this->assertSame(['one', 'two', '03'], $input->getPath());
        $this->assertSame(['one', 'two'], $input->getTarget());
        $this->assertTrue($input->hasNext()); // 3 é o próximo
        $this->assertSame([
            0 => 3,
            'x' => 'four',
            'y' => 'five',
            'z' => 'six'
        ], $input->toArray());

        // - - - - - - - - - - - - - - - - - - - - - - -

        $input->next(); // adiciona 3

        $this->assertSame('GET', $input->getMethod());
        $this->assertSame('one/two/03', $input->getPathString());
        $this->assertSame(['one', 'two', '03'], $input->getPath());
        $this->assertSame(['one', 'two', '03'], $input->getTarget());
        $this->assertFalse($input->hasNext()); // não há próximo
        $this->assertSame([
            'x' => 'four',
            'y' => 'five',
            'z' => 'six'
        ], $input->toArray());

        // - - - - - - - - - - - - - - - - - - - - - - -

        $input->next(); // não há mais o que adicionar

        $this->assertSame('GET', $input->getMethod());
        $this->assertSame('one/two/03', $input->getPathString());
        $this->assertSame(['one', 'two', '03'], $input->getPath());
        $this->assertSame(['one', 'two', '03'], $input->getTarget());
        $this->assertFalse($input->hasNext()); // não há próximo
        $this->assertSame([
            'x' => 'four',
            'y' => 'five',
            'z' => 'six'
        ], $input->toArray());
    }

    /** @test */
    public function reset(): void
    {
        $input = Input::fromString('/one/two/03?x=four&y=five&z=six');

        $this->assertSame('GET', $input->getMethod());
        $this->assertSame([
            0 => 'two',
            1 => 3,
            'x' => 'four',
            'y' => 'five',
            'z' => 'six'
        ], $input->toArray());
        $this->assertSame(['one', 'two', '03'], $input->getPath());
        $this->assertSame(['one'], $input->getTarget());
        $this->assertTrue($input->hasNext()); // two é o próximo

        $input->next(); // adiciona two
        $input->next(); // adiciona 3

        $this->assertSame('GET', $input->getMethod());
        $this->assertSame([
            'x' => 'four',
            'y' => 'five',
            'z' => 'six'
        ], $input->toArray());
        $this->assertSame(['one', 'two', '03'], $input->getPath());
        $this->assertSame(['one', 'two', '03'], $input->getTarget());
        $this->assertFalse($input->hasNext()); // não há próximo

        $input->reset(); // restaura todos
 
        $this->assertSame('GET', $input->getMethod());
        $this->assertSame([
            0 => 'two',
            1 => 3,
            'x' => 'four',
            'y' => 'five',
            'z' => 'six'
        ], $input->toArray());
        $this->assertSame(['one', 'two', '03'], $input->getPath());
        $this->assertSame(['one'], $input->getTarget());
        $this->assertTrue($input->hasNext()); // two é o próximo
    }

    /** @test */
    public function getParam(): void
    {
        $input = Input::fromString('/one/two/03?x=four&y=1&z=1.1');

        $this->assertSame([
            0 => 'two',
            1 => 3,
            'x' => 'four',
            'y' => 1,
            'z' => 1.1
        ], $input->toArray());

        $this->assertSame('two', $input->param(0));
        $this->assertSame(3, $input->param(1));
        $this->assertSame('four', $input->param('x'));
        $this->assertSame(1, $input->param('y'));
        $this->assertSame(1.1, $input->param('z'));

        $input->next();

        $this->assertSame([
            0 => 3,
            'x' => 'four',
            'y' => 1,
            'z' => 1.1
        ], $input->toArray());

        $this->assertSame(3, $input->param(0));
        $this->assertSame('four', $input->param('x'));
        $this->assertSame(1, $input->param('y'));
        $this->assertSame(1.1, $input->param('z'));

        $input->next();

        $this->assertSame([
            'x' => 'four',
            'y' => 1,
            'z' => 1.1
        ], $input->toArray());
        $this->assertNull($input->param(0));
        $this->assertSame('four', $input->param('x'));
        $this->assertSame(1, $input->param('y'));
        $this->assertSame(1.1, $input->param('z'));

        // não muda nada
        $input->next();

        $this->assertSame([
            'x' => 'four',
            'y' => 1,
            'z' => 1.1
        ], $input->toArray());
        $this->assertNull($input->param(0));
        $this->assertSame('four', $input->param('x'));
        $this->assertSame(1, $input->param('y'));
        $this->assertSame(1.1, $input->param('z'));
    }

    /** @test */
    public function inputToString(): void
    {
        $input = Input::fromString('/one/two/three?x=four&y=1&z=1.1');

        $this->assertSame('one/two/three', $input->getPathString());

        $this->assertSame(['one'], $input->getTarget());

        // assume que o alvo é 'one'
        $this->assertSame([
            0 => 'two',
            1 => 'three',
            'x' => 'four',
            'y' => 1,
            'z' => 1.1
        ], $input->toArray());

        $this->assertSame(
            '0=two&1=three&x=four&y=1&z=1.1',
            (string)$input
        );

        $input->next();

        $this->assertSame([
            0 => 'three',
            'x' => 'four',
            'y' => 1,
            'z' => 1.1
        ], $input->toArray());

        $this->assertSame(
            '0=three&x=four&y=1&z=1.1',
            (string)$input
        );
    }

    /** @test */
    public function appendParams(): void
    {
        $input = Input::fromString('/one/two/03?x=four&y=five&z=six');

        $this->assertSame('GET', $input->getMethod());
        $this->assertSame('one/two/03', $input->getPathString());
        $this->assertSame(['one', 'two', '03'], $input->getPath());
        $this->assertSame(['one'], $input->getTarget());
        $this->assertTrue($input->hasNext()); // two é o próximo

        $this->assertSame([
            0 => 'two',
            1 => 3,
            'x' => 'four',
            'y' => 'five',
            'z' => 'six'
        ], $input->toArray());

        $input->appendParams(['id' => 99, 'name' => 'Teste']);

        $this->assertSame([
            0 => 'two',
            1 => 3,
            'x' => 'four',
            'y' => 'five',
            'z' => 'six',
            'id' => 99,
            'name' => 'Teste'
        ], $input->toArray());
    }
}
