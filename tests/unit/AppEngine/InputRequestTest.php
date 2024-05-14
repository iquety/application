<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine;

use Iquety\Application\AppEngine\FileSet;
use Iquety\Application\AppEngine\Input;
use Tests\Unit\TestCase;

class InputRequestTest extends TestCase
{
    /**
     * Devolve uma estrutura com 1 arquivo no formato que o PHP recebe 
     * os uploads enviados via HTTP
     */
    private function phpSingleFile(): array
    {
        return [
            'inputFile' => [
                "name"      => "attachment.gif",
                "full_path" => "attachment.gif",
                'type'      => "image/gif",
                "tmp_name"  => __DIR__ . "/attachment.gif",
                "error"     => 0,
                "size"      => 173
            ]
        ];
    }

    /** @test */
    public function fromPostRequest(): void
    {
        $request = (new RequestFactory())->makeRequest(
            'POST',
            '/one/two/three',
            'x=four&y=five&z=six',
            ['name' => 'test'],
            $this->phpSingleFile()
        );

        $input = Input::fromRequest($request);

        $this->assertCount(8, $input->toArray());
        $this->assertSame('one', $input->param(0));
        $this->assertSame('two', $input->param(1));
        $this->assertSame('three', $input->param(2));
        $this->assertSame('four', $input->param('x'));
        $this->assertSame('five', $input->param('y'));
        $this->assertSame('six', $input->param('z'));
        $this->assertSame('test', $input->param('name'));
        $this->assertInstanceOf(FileSet::class, $input->param('inputFile'));
    }

    /** @test */
    public function fromGetRequest(): void
    {
        $request = (new RequestFactory())->makeRequest(
            'GET',
            '/one/two/three',
            'x=four&y=five&z=six',
            ['name' => 'test'],
            $this->phpSingleFile()
        );

        $input = Input::fromRequest($request);

        $this->assertCount(8, $input->toArray());
        $this->assertSame('one', $input->param(0));
        $this->assertSame('two', $input->param(1));
        $this->assertSame('three', $input->param(2));
        $this->assertSame('four', $input->param('x'));
        $this->assertSame('five', $input->param('y'));
        $this->assertSame('six', $input->param('z'));
        $this->assertSame('test', $input->param('name'));
        $this->assertInstanceOf(FileSet::class, $input->param('inputFile'));
    }

    /** @test */
    public function inputGetToString(): void
    {
        $request = (new RequestFactory())->makeRequest(
            'GET',
            '/one/two/three',
            'x=four&y=1&z=1.1',
            ['name' => 'test'],
            $this->phpSingleFile()
        );

        $input = Input::fromRequest($request);

        $this->assertCount(8, $input->toArray());
        $this->assertSame('one', $input->param(0));
        $this->assertSame('two', $input->param(1));
        $this->assertSame('three', $input->param(2));
        $this->assertSame('four', $input->param('x'));
        $this->assertSame(1, $input->param('y'));
        $this->assertSame(1.1, $input->param('z'));
        $this->assertSame('test', $input->param('name'));
        $this->assertInstanceOf(FileSet::class, $input->param('inputFile'));

        $this->assertSame(
            '0=one&1=two&2=three&x=four&y=1&z=1.1&name=test&inputFile=attachment.gif',
            (string)$input
        );
    }

    /** @test */
    public function inputPostToString(): void
    {
        $request = (new RequestFactory())->makeRequest(
            'POST',
            '/one/two/three',
            'x=four&y=1&z=1.1',
            ['name' => 'test'],
            $this->phpSingleFile()
        );

        $input = Input::fromRequest($request);

        $this->assertCount(8, $input->toArray());
        $this->assertSame('one', $input->param(0));
        $this->assertSame('two', $input->param(1));
        $this->assertSame('three', $input->param(2));
        $this->assertSame('four', $input->param('x'));
        $this->assertSame(1, $input->param('y'));
        $this->assertSame(1.1, $input->param('z'));
        $this->assertSame('test', $input->param('name'));
        $this->assertInstanceOf(FileSet::class, $input->param('inputFile'));

        $this->assertSame(
            '0=one&1=two&2=three&x=four&y=1&z=1.1&name=test&inputFile=attachment.gif',
            (string)$input
        );
    }
}
