<?php

declare(strict_types=1);

namespace Tests\IoEngine;

use Iquety\Application\IoEngine\FileSet;
use Iquety\Application\IoEngine\Action\Input;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class InputFilesTest extends TestCase
{
    /**
     * Devolve uma estrutura com 3 arquivos no formato que o PHP recebe
     * os uploads enviados via HTTP
     * @return array<string,array<string,mixed>>
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
    public function singleFile(): void
    {
        $request = (new RequestFactory())
            ->makeRequest('POST', '/one/two', 'x=1&y=2', ['name' => 'test'], $this->phpSingleFile());

        $input = Input::fromRequest($request);

        $this->assertSame(['one', 'two'], $input->getPath());
        $this->assertSame(['one'], $input->getTarget());
        $this->assertSame('two', $input->param(0));
        $this->assertSame(1, $input->param('x'));
        $this->assertSame(2, $input->param('y'));
        $this->assertSame('test', $input->param('name'));

        /** @var FileSet */
        $fileSetOne = $input->param('inputFile');

        $this->assertInstanceOf(FileSet::class, $fileSetOne);
        $this->assertCount(1, $fileSetOne->toArray());

        $this->assertSame('attachment.gif', $fileSetOne->toArray()[0]->getName());
        $this->assertSame('image/gif', $fileSetOne->toArray()[0]->getMimeType());
        $this->assertSame(173, $fileSetOne->toArray()[0]->getSize());
        $this->assertSame(
            'There is no error, the file uploaded with success',
            $fileSetOne->toArray()[0]->getErrorMessage()
        );
        $this->assertFalse($fileSetOne->toArray()[0]->hasError());
        $this->assertSame(
            file_get_contents(__DIR__ . '/attachment.gif'),
            $fileSetOne->toArray()[0]->getContent()
        );

        /** @var FileSet */
        $fileSetTwo = $input->param('inputFile2');

        $this->assertInstanceOf(FileSet::class, $fileSetTwo);
        $this->assertCount(1, $fileSetTwo->toArray());

        $this->assertSame('attachment.png', $fileSetTwo->toArray()[0]->getName());
        $this->assertSame('image/png', $fileSetTwo->toArray()[0]->getMimeType());
        $this->assertSame(222, $fileSetTwo->toArray()[0]->getSize());
        $this->assertSame(
            'There is no error, the file uploaded with success',
            $fileSetTwo->toArray()[0]->getErrorMessage()
        );
        $this->assertFalse($fileSetTwo->toArray()[0]->hasError());
        $this->assertSame(
            file_get_contents(__DIR__ . '/attachment.png'),
            $fileSetTwo->toArray()[0]->getContent()
        );
    }

    /** @test */
    public function multipleFiles(): void
    {
        $request = (new RequestFactory())
            ->makeRequest('POST', '/one/two', 'x=1&y=2', ['name' => 'test'], $this->phpMultiFiles());

        $input = Input::fromRequest($request);

        $this->assertSame(['one', 'two'], $input->getPath());
        $this->assertSame(['one'], $input->getTarget());
        $this->assertSame('two', $input->param(0));
        $this->assertSame(1, $input->param('x'));
        $this->assertSame(2, $input->param('y'));
        $this->assertSame('test', $input->param('name'));

        /** @var FileSet */
        $fileSetOne = $input->param('inputFile');

        $this->assertInstanceOf(FileSet::class, $fileSetOne);
        $this->assertCount(3, $fileSetOne->toArray());

        $this->assertSame('attachment.gif', $fileSetOne->toArray()[0]->getName());
        $this->assertSame('image/gif', $fileSetOne->toArray()[0]->getMimeType());
        $this->assertSame(173, $fileSetOne->toArray()[0]->getSize());
        $this->assertSame(
            'There is no error, the file uploaded with success',
            $fileSetOne->toArray()[0]->getErrorMessage()
        );
        $this->assertFalse($fileSetOne->toArray()[0]->hasError());
        $this->assertSame(
            file_get_contents(__DIR__ . '/attachment.gif'),
            $fileSetOne->toArray()[0]->getContent()
        );

        $this->assertSame('attachment.jpg', $fileSetOne->toArray()[1]->getName());
        $this->assertSame('image/jpeg', $fileSetOne->toArray()[1]->getMimeType());
        $this->assertSame(4710, $fileSetOne->toArray()[1]->getSize());
        $this->assertSame(
            'There is no error, the file uploaded with success',
            $fileSetOne->toArray()[1]->getErrorMessage()
        );
        $this->assertFalse($fileSetOne->toArray()[1]->hasError());
        $this->assertSame(
            file_get_contents(__DIR__ . '/attachment.jpg'),
            $fileSetOne->toArray()[1]->getContent()
        );

        $this->assertSame('attachment.png', $fileSetOne->toArray()[2]->getName());
        $this->assertSame('image/png', $fileSetOne->toArray()[2]->getMimeType());
        $this->assertSame(222, $fileSetOne->toArray()[2]->getSize());
        $this->assertSame(
            'There is no error, the file uploaded with success',
            $fileSetOne->toArray()[2]->getErrorMessage()
        );
        $this->assertFalse($fileSetOne->toArray()[2]->hasError());
        $this->assertSame(
            file_get_contents(__DIR__ . '/attachment.png'),
            $fileSetOne->toArray()[2]->getContent()
        );

        /** @var FileSet */
        $fileSetTwo = $input->param('inputFile2');

        $this->assertInstanceOf(FileSet::class, $fileSetTwo);
        $this->assertCount(1, $fileSetTwo->toArray());

        $this->assertSame('attachment.png', $fileSetTwo->toArray()[0]->getName());
        $this->assertSame('image/png', $fileSetTwo->toArray()[0]->getMimeType());
        $this->assertSame(222, $fileSetTwo->toArray()[0]->getSize());
        $this->assertSame(
            'There is no error, the file uploaded with success',
            $fileSetTwo->toArray()[0]->getErrorMessage()
        );
        $this->assertFalse($fileSetTwo->toArray()[0]->hasError());
        $this->assertSame(
            file_get_contents(__DIR__ . '/attachment.png'),
            $fileSetTwo->toArray()[0]->getContent()
        );
    }
}
