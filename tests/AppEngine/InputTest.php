<?php

declare(strict_types=1);

namespace Tests\AppEngine;

use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\Input;
use Iquety\Application\Http\HttpFactory;
use Tests\TestCase;

class InputTest extends TestCase
{
    /** @test */
    public function firstParam(): void
    {
        $input = new Input([ 23, 33, 43, 53 ]);

        $this->assertEquals(23, $input->first());
    }

    /** @test */
    public function specificParam(): void
    {
        $input = new Input([ 23, 33, 43, 53 ]);

        $this->assertSame(23, $input->param(0));
        $this->assertSame(33, $input->param(1));
        $this->assertSame(43, $input->param(2));
        $this->assertSame(53, $input->param(3));
        $this->assertNull($input->param(4));
        $this->assertNull($input->param(99));
    }

    /** @test */
    public function paramToString(): void
    {
        $input = new Input([ 23, 33, 43, 53 ]);

        $this->assertSame('23,33,43,53', (string)$input);

        $input = new Input([ 23 ]);

        $this->assertSame('23', (string)$input);
    }
}
