<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine;

use Iquety\Application\AppEngine\UriParser;
use Tests\Unit\TestCase;

class UriParserTest extends TestCase
{
    public function fromStringProvider(): array
    {
        $list = [];

        $list['empty'] = ['', []];
        $list['/'] = ['/', []];
        $list['/?x=1&y=2'] = ['/?x=1&y=2',     ['x' => 1, 'y' => 2]];
        $list['/?x=1.0&y=2'] = ['/?x=1.0&y=2', ['x' => 1.0, 'y' => 2]];
        $list['/?x=1&y=2.0'] = ['/?x=1&y=2.0', ['x' => 1, 'y' => 2.0]];
        $list['/?x=1.1&y=2'] = ['/?x=1.1&y=2', ['x' => 1.1, 'y' => 2]];
        $list['/?x=1&y=2.1'] = ['/?x=1&y=2.1', ['x' => 1, 'y' => 2.1]];

        $list['one']       = ['one',   [0 => 'one']];
        $list['/one']      = ['/one',  [0 => 'one']];
        $list['one/']      = ['one/',  [0 => 'one']];
        $list['/one/']     = ['/one/', [0 => 'one']];

        $list['7']   = ['7',   [0 => 7]];
        $list['7.0'] = ['7.0', [0 => 7.0]];
        $list['7.1'] = ['7.1', [0 => 7.1]];

        $list['one/7']   = ['one/7',   [0 => 'one', 1 => 7]];
        $list['one/7.0'] = ['one/7.0', [0 => 'one', 1 => 7.0]];
        $list['one/7.1'] = ['one/7.1', [0 => 'one', 1 => 7.1]];

        $list['one/7/8']   = ['one/7/8',   [0 => 'one', 1 => 7, 2 => 8]];
        $list['one/7/8.0'] = ['one/7/8.0', [0 => 'one', 1 => 7, 2 => 8.0]];
        $list['one/7/8.1'] = ['one/7/8.1', [0 => 'one', 1 => 7, 2 => 8.1]];

        $list['one/7.0/8'] = ['one/7.0/8', [0 => 'one', 1 => 7.0, 2 => 8]];
        $list['one/7.1/8'] = ['one/7.1/8', [0 => 'one', 1 => 7.1, 2 => 8]];

        $list['one/two']   = ['one/two',   [0 => 'one', 1 => 'two']];
        $list['/one/two']  = ['/one/two',  [0 => 'one', 1 => 'two']];
        $list['one/two/']  = ['one/two/',  [0 => 'one', 1 => 'two']];
        $list['/one/two/'] = ['/one/two/', [0 => 'one', 1 => 'two']];

        $list['one?x=1']       = ['one?x=1',   [0 => 'one', 'x' => 1]];
        $list['/one?x=1']      = ['/one?x=1',  [0 => 'one', 'x' => 1]];
        $list['one/?x=1']      = ['one/?x=1',  [0 => 'one', 'x' => 1]];
        $list['/one/?x=1']     = ['/one/?x=1', [0 => 'one', 'x' => 1]];

        $list['one?x=1&y=2']   = ['one?x=1&y=2',   [0 => 'one', 'x' => 1, 'y' => 2]];
        $list['/one?x=1&y=2']  = ['/one?x=1&y=2',  [0 => 'one', 'x' => 1, 'y' => 2]];
        $list['one/?x=1&y=2']  = ['one/?x=1&y=2',  [0 => 'one', 'x' => 1, 'y' => 2]];
        $list['/one/?x=1&y=2'] = ['/one/?x=1&y=2', [0 => 'one', 'x' => 1, 'y' => 2]];
        
        $list['one/two?x=1']   = ['one/two?x=1',   [0 => 'one', 1 => 'two', 'x' => 1]];
        $list['/one/two?x=1']  = ['/one/two?x=1',  [0 => 'one', 1 => 'two', 'x' => 1]];
        $list['one/two/?x=1']  = ['one/two/?x=1',  [0 => 'one', 1 => 'two', 'x' => 1]];
        $list['/one/two/?x=1'] = ['/one/two/?x=1', [0 => 'one', 1 => 'two', 'x' => 1]];

        $list['one/two?x=1&y=2']   = ['one/two?x=1&y=2',   [0 => 'one', 1 => 'two', 'x' => 1, 'y' => 2]];
        $list['/one/two?x=1&y=2']  = ['/one/two?x=1&y=2',  [0 => 'one', 1 => 'two', 'x' => 1, 'y' => 2]];
        $list['one/two/?x=1&y=2']  = ['one/two/?x=1&y=2',  [0 => 'one', 1 => 'two', 'x' => 1, 'y' => 2]];
        $list['/one/two/?x=1&y=2'] = ['/one/two/?x=1&y=2', [0 => 'one', 1 => 'two', 'x' => 1, 'y' => 2]];

        return $list;
    }

    /** 
     * @test 
     * @dataProvider fromStringProvider
     * @param array<int|string,int|string> $paramList
     */
    public function fromString(string $uri, array $paramList): void
    {
        $parser = new UriParser($uri);

        $this->assertEquals($paramList, $parser->toArray());
    }

    // /** @test */
    // public function specificParam(): void
    // {
    //     $input = new Input([ 23, 33, 43, 53 ]);

    //     $this->assertSame(23, $input->param(0));
    //     $this->assertSame(33, $input->param(1));
    //     $this->assertSame(43, $input->param(2));
    //     $this->assertSame(53, $input->param(3));
    //     $this->assertNull($input->param(4));
    //     $this->assertNull($input->param(99));
    // }

    // /** @test */
    // public function paramToString(): void
    // {
    //     $input = new Input([ 23, 33, 43, 53 ]);

    //     $this->assertSame('23,33,43,53', (string)$input);

    //     $input = new Input([ 23 ]);

    //     $this->assertSame('23', (string)$input);
    // }
}
