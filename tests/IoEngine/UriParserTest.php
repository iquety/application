<?php

declare(strict_types=1);

namespace Tests\IoEngine;

use Iquety\Application\IoEngine\UriParser;
use Tests\TestCase;

class UriParserTest extends TestCase
{
    /** @return array<float|int|string,array<int,mixed>> */
    public function fromStringProvider(): array
    {
        $list = [];

        $list['empty']       = ['', '', []];
        $list['empty space'] = [' ', '', []];
        $list['one bar']     = ['/', '', []];

        $list['one bar left space']  = [ ' /', '', [] ];
        $list['one bar right space'] = [ ' /', '', [] ];
        $list['one bar both spaces'] = [ ' / ', '', [] ];

        $list['/?x=1&y=2']   = ['/?x=1&y=2',     '', ['x' => 1, 'y' => 2]];
        $list['/?x=1.0&y=2'] = ['/?x=1.0&y=2', '', ['x' => 1.0, 'y' => 2]];
        $list['/?x=1&y=2.0'] = ['/?x=1&y=2.0', '', ['x' => 1, 'y' => 2.0]];
        $list['/?x=1.1&y=2'] = ['/?x=1.1&y=2', '', ['x' => 1.1, 'y' => 2]];
        $list['/?x=1&y=2.1'] = ['/?x=1&y=2.1', '', ['x' => 1, 'y' => 2.1]];

        $list['?x=1&y=2']    = ['?x=1&y=2',     '', ['x' => 1, 'y' => 2]];
        $list['?x=1.0&y=2']  = ['?x=1.0&y=2', '', ['x' => 1.0, 'y' => 2]];
        $list['?x=1&y=2.0']  = ['?x=1&y=2.0', '', ['x' => 1, 'y' => 2.0]];
        $list['?x=1.1&y=2']  = ['?x=1.1&y=2', '', ['x' => 1.1, 'y' => 2]];
        $list['?x=1&y=2.1']  = ['?x=1&y=2.1', '', ['x' => 1, 'y' => 2.1]];

        $list['one']       = ['one',   'one', [0 => 'one']];
        $list['/one']      = ['/one',  'one', [0 => 'one']];
        $list['one/']      = ['one/',  'one', [0 => 'one']];
        $list['/one/']     = ['/one/', 'one', [0 => 'one']];

        $list['7']   = ['7',   '7',   [0 => 7]];
        $list['7.0'] = ['7.0', '7.0', [0 => 7.0]];
        $list['7.1'] = ['7.1', '7.1', [0 => 7.1]];

        $list['one/7']   = ['one/7',   'one/7',   [0 => 'one', 1 => 7]];
        $list['one/7.0'] = ['one/7.0', 'one/7.0', [0 => 'one', 1 => 7.0]];
        $list['one/7.1'] = ['one/7.1', 'one/7.1', [0 => 'one', 1 => 7.1]];

        $list['one/7/8']   = ['one/7/8',   'one/7/8',   [0 => 'one', 1 => 7, 2 => 8]];
        $list['one/7/8.0'] = ['one/7/8.0', 'one/7/8.0', [0 => 'one', 1 => 7, 2 => 8.0]];
        $list['one/7/8.1'] = ['one/7/8.1', 'one/7/8.1', [0 => 'one', 1 => 7, 2 => 8.1]];

        $list['one/7.0/8'] = ['one/7.0/8', 'one/7.0/8', [0 => 'one', 1 => 7.0, 2 => 8]];
        $list['one/7.1/8'] = ['one/7.1/8', 'one/7.1/8', [0 => 'one', 1 => 7.1, 2 => 8]];

        $list['one/two']   = ['one/two',   'one/two', [0 => 'one', 1 => 'two']];
        $list['/one/two']  = ['/one/two',  'one/two', [0 => 'one', 1 => 'two']];
        $list['one/two/']  = ['one/two/',  'one/two', [0 => 'one', 1 => 'two']];
        $list['/one/two/'] = ['/one/two/', 'one/two', [0 => 'one', 1 => 'two']];

        $list['one?x=1']       = ['one?x=1',   'one', [0 => 'one', 'x' => 1]];
        $list['/one?x=1']      = ['/one?x=1',  'one', [0 => 'one', 'x' => 1]];
        $list['one/?x=1']      = ['one/?x=1',  'one', [0 => 'one', 'x' => 1]];
        $list['/one/?x=1']     = ['/one/?x=1', 'one', [0 => 'one', 'x' => 1]];

        $list['one?x=1&y=2']   = ['one?x=1&y=2',   'one', [0 => 'one', 'x' => 1, 'y' => 2]];
        $list['/one?x=1&y=2']  = ['/one?x=1&y=2',  'one', [0 => 'one', 'x' => 1, 'y' => 2]];
        $list['one/?x=1&y=2']  = ['one/?x=1&y=2',  'one', [0 => 'one', 'x' => 1, 'y' => 2]];
        $list['/one/?x=1&y=2'] = ['/one/?x=1&y=2', 'one', [0 => 'one', 'x' => 1, 'y' => 2]];

        $list['one/two?x=1']   = ['one/two?x=1',   'one/two', [0 => 'one', 1 => 'two', 'x' => 1]];
        $list['/one/two?x=1']  = ['/one/two?x=1',  'one/two', [0 => 'one', 1 => 'two', 'x' => 1]];
        $list['one/two/?x=1']  = ['one/two/?x=1',  'one/two', [0 => 'one', 1 => 'two', 'x' => 1]];
        $list['/one/two/?x=1'] = ['/one/two/?x=1', 'one/two', [0 => 'one', 1 => 'two', 'x' => 1]];

        $list['one/two?x=1&y=2']   = ['one/two?x=1&y=2',   'one/two', [0 => 'one', 1 => 'two', 'x' => 1, 'y' => 2]];
        $list['/one/two?x=1&y=2']  = ['/one/two?x=1&y=2',  'one/two', [0 => 'one', 1 => 'two', 'x' => 1, 'y' => 2]];
        $list['one/two/?x=1&y=2']  = ['one/two/?x=1&y=2',  'one/two', [0 => 'one', 1 => 'two', 'x' => 1, 'y' => 2]];
        $list['/one/two/?x=1&y=2'] = ['/one/two/?x=1&y=2', 'one/two', [0 => 'one', 1 => 'two', 'x' => 1, 'y' => 2]];

        return $list;
    }

    /**
     * @test
     * @dataProvider fromStringProvider
     * @param array<int|string,int|string> $paramList
     */
    public function fromString(string $uri, string $path, array $paramList): void
    {
        $parser = new UriParser($uri);

        $this->assertEquals($paramList, $parser->toArray());

        $this->assertEquals($path, implode('/', $parser->getPath()));
    }

    /** @test */
    public function fromStringArray(): void
    {
        $parser = new UriParser('/test?name[]=one&name[]=1&name[]=1.0');

        $this->assertSame([
            0 => 'test',
            'name' => [
                0 => 'one',
                1 => 1,
                2 => 1.0
            ]
        ], $parser->toArray());
    }
}
