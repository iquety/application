<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine;

use Iquety\Application\AppEngine\Input;
use Tests\Unit\TestCase;

class InputStringTest extends TestCase
{
    /** @test */
    public function fromString(): void
    {
        $input = Input::fromString('/one/two/three?x=four&y=five&z=six');

        $this->assertEquals([
            0 => 'one',
            1 => 'two',
            2 => 'three',
            'x' => 'four',
            'y' => 'five',
            'z' => 'six'
        ], $input->toArray());
    }

    /** @test */
    public function next(): void
    {
        $input = Input::fromString('/one/two/three?x=four&y=five&z=six');

        $this->assertEquals([
            0 => 'one',
            1 => 'two',
            2 => 'three',
            'x' => 'four',
            'y' => 'five',
            'z' => 'six'
        ], $input->toArray());

        $input->next(); // remove z

        $this->assertEquals([
            0 => 'one',
            1 => 'two',
            2 => 'three',
            'x' => 'four',
            'y' => 'five',
        ], $input->toArray());

        $input->next(); // remove y
        $input->next(); // remove x

        $this->assertEquals([
            0 => 'one',
            1 => 'two',
            2 => 'three',
        ], $input->toArray());

        $input->next(); // remove 2
        $input->next(); // remove 1
        $input->next(); // remove 0

        $this->assertEquals([], $input->toArray());

        $input->next(); // remove nada

        $this->assertEquals([], $input->toArray());
    }

    /** @test */
    public function reset(): void
    {
        $input = Input::fromString('/one/two/three?x=four&y=five&z=six');

        $this->assertEquals([
            0 => 'one',
            1 => 'two',
            2 => 'three',
            'x' => 'four',
            'y' => 'five',
            'z' => 'six'
        ], $input->toArray());

        $input->next(); // remove z
        $input->next(); // remove y
        $input->next(); // remove z

        $this->assertEquals([
            0 => 'one',
            1 => 'two',
            2 => 'three'
        ], $input->toArray());

        $input->reset(); // restaura todos
 
        $this->assertEquals([
            0 => 'one',
            1 => 'two',
            2 => 'three',
            'x' => 'four',
            'y' => 'five',
            'z' => 'six'
        ], $input->toArray());
    }

    /** @test */
    public function getParam(): void
    {
        $input = Input::fromString('/one/two/three?x=four&y=1&z=1.1');

        $this->assertEquals([
            0 => 'one',
            1 => 'two',
            2 => 'three',
            'x' => 'four',
            'y' => 1,
            'z' => 1.1
        ], $input->toArray());

        $this->assertSame('one', $input->param(0));
        $this->assertSame('two', $input->param(1));
        $this->assertSame('three', $input->param(2));
        $this->assertSame('four', $input->param('x'));
        $this->assertSame(1, $input->param('y'));
        $this->assertSame(1.1, $input->param('z'));

        $input->next(); // remove z

        $this->assertNull($input->param('z'));

        $input->next(); // remove y
        $input->next(); // remove x
        $input->next(); // remove 2

        $this->assertNull($input->param(2));
    }

    /** @test */
    public function inputToString(): void
    {
        $input = Input::fromString('/one/two/three?x=four&y=1&z=1.1');

        $this->assertEquals([
            0 => 'one',
            1 => 'two',
            2 => 'three',
            'x' => 'four',
            'y' => 1,
            'z' => 1.1
        ], $input->toArray());

        $this->assertSame(
            '0=one&1=two&2=three&x=four&y=1&z=1.1',
            (string)$input
        );
    }
}