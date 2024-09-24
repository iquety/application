<?php

declare(strict_types=1);

namespace Tests\IoEngine;

use Iquety\Application\IoEngine\FileSet;
use Iquety\Application\IoEngine\Action\Input;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class InputRequestTest extends TestCase
{
    /**
     * Devolve uma estrutura com 1 arquivo no formato que o PHP recebe
     * os uploads enviados via HTTP
     * @return array<string,array<string,int|string>>
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
            '/one/two/3',
            'x=four&y=five&z=six',
            ['name' => 'test'],
            $this->phpSingleFile()
        );

        $input = Input::fromRequest($request);

        $this->assertSame('POST', $input->getMethod());
        $this->assertSame('one/two/3', $input->getPathString());
        $this->assertSame(['one', 'two', '3'], $input->getPath());
        $this->assertSame(['one'], $input->getTarget());

        $this->assertCount(7, $input->toArray());
        $this->assertSame('two', $input->param(0));
        $this->assertSame(3, $input->param(1));
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
            '/one/two/3',
            'x=four&y=five&z=six',
            ['name' => 'test'],
            $this->phpSingleFile()
        );

        $input = Input::fromRequest($request);

        $this->assertSame('GET', $input->getMethod());
        $this->assertSame('one/two/3', $input->getPathString());
        $this->assertSame(['one', 'two', '3'], $input->getPath());
        $this->assertSame(['one'], $input->getTarget());

        $this->assertCount(7, $input->toArray());
        $this->assertSame('two', $input->param(0));
        $this->assertSame(3, $input->param(1));
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

        $this->assertSame('GET', $input->getMethod());
        $this->assertCount(7, $input->toArray());
        $this->assertSame('two', $input->param(0));
        $this->assertSame('three', $input->param(1));
        $this->assertSame('four', $input->param('x'));
        $this->assertSame(1, $input->param('y'));
        $this->assertSame(1.1, $input->param('z'));
        $this->assertSame('test', $input->param('name'));
        $this->assertInstanceOf(FileSet::class, $input->param('inputFile'));

        $this->assertSame(
            '0=two&1=three&x=four&y=1&z=1.1&name=test&inputFile=attachment.gif',
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

        $this->assertSame('POST', $input->getMethod());
        $this->assertCount(7, $input->toArray());
        $this->assertSame('two', $input->param(0));
        $this->assertSame('three', $input->param(1));
        $this->assertSame('four', $input->param('x'));
        $this->assertSame(1, $input->param('y'));
        $this->assertSame(1.1, $input->param('z'));
        $this->assertSame('test', $input->param('name'));
        $this->assertInstanceOf(FileSet::class, $input->param('inputFile'));

        $this->assertSame(
            '0=two&1=three&x=four&y=1&z=1.1&name=test&inputFile=attachment.gif',
            (string)$input
        );
    }

    /** @test */
    public function appendParams(): void
    {
        $request = (new RequestFactory())->makeRequest(
            'POST',
            '/one/two/3',
            'x=four&y=five&z=six',
            ['name' => 'test'],
            $this->phpSingleFile()
        );

        $input = Input::fromRequest($request);

        $this->assertSame('POST', $input->getMethod());
        $this->assertSame('one/two/3', $input->getPathString());
        $this->assertSame(['one', 'two', '3'], $input->getPath());
        $this->assertSame(['one'], $input->getTarget());

        $this->assertCount(7, $input->toArray());
        $this->assertSame('two', $input->param(0));
        $this->assertSame(3, $input->param(1));
        $this->assertSame('four', $input->param('x'));
        $this->assertSame('five', $input->param('y'));
        $this->assertSame('six', $input->param('z'));
        $this->assertSame('test', $input->param('name'));
        $this->assertInstanceOf(FileSet::class, $input->param('inputFile'));

        $input->appendParams(['id' => 99, 'nine' => 'Teste']);

        $this->assertCount(9, $input->toArray());
        $this->assertSame('two', $input->param(0));
        $this->assertSame(3, $input->param(1));
        $this->assertSame('four', $input->param('x'));
        $this->assertSame('five', $input->param('y'));
        $this->assertSame('six', $input->param('z'));
        $this->assertSame('test', $input->param('name'));
        $this->assertInstanceOf(FileSet::class, $input->param('inputFile'));
        $this->assertSame(99, $input->param('id'));
        $this->assertSame('Teste', $input->param('nine'));
    }
}
