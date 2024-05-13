<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine;

use Iquety\Application\AppEngine\FileSet;
use Iquety\Application\AppEngine\Input;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\UploadedFile;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use Tests\Unit\TestCase;

class InputFilesTest extends TestCase
{
    /**
     * Devolve uma estrutura com 3 arquivos no formato que o PHP recebe 
     * os uploads enviados via HTTP
     */
    private function phpMultiFiles(): array
    {
        return [
            'inputFile' => [
                "name" => [
                    0 => "attachment.gif",
                    1 => "attachment.jpg",
                    2 => "attachment.png"
                ],
                "full_path" => [
                    0 => "attachment.gif",
                    1 => "attachment.jpg",
                    2 => "attachment.png"
                ],
                'type' => [
                    0 => "image/gif",
                    1 => "image/jpeg",
                    2 => "image/png"
                ],
                "tmp_name" => [
                    0 => __DIR__ . "/attachment.gif", // simula os arquivos
                    1 => __DIR__ . "/attachment.jpg", // armazenados em /tmp
                    2 => __DIR__ . "/attachment.png", // Ex: /tmp/phpntcN4T
                ],
                "error" => [
                    0 => 0,
                    1 => 0,
                    2 => 0,
                ],
                "size" => [
                    0 => 173,
                    1 => 4710,
                    2 => 222
                ]
            ],
            'inputFile2' => [
                "name"      => "attachment.png",
                "full_path" => "attachment.png",
                'type'      => "image/png",
                "tmp_name"  => __DIR__ . "/attachment.png",
                "error"     => 0,
                "size"      => 222
            ]
        ];
    }

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
            ], 
            'inputFile2' => [
                "name"      => "attachment.png",
                "full_path" => "attachment.png",
                'type'      => "image/png",
                "tmp_name"  => __DIR__ . "/attachment.png",
                "error"     => 0,
                "size"      => 222
            ], 
        ];
    }

    /** @test */
    public function fromRequest(): void
    {
        $request = (new RequestFactory())
            ->makeRequest('POST', '/one/two', 'x=1&y=2', ['name' => 'test'], $this->phpSingleFile());

        $input = Input::fromRequest($request);

        $this->assertSame('one', $input->param(0));
        $this->assertSame('two', $input->param(1));
        $this->assertSame(1, $input->param('x'));
        $this->assertSame(2, $input->param('y'));
        $this->assertSame('test', $input->param('name'));
        $this->assertInstanceOf(FileSet::class, $input->param('inputFile'));
    }
}