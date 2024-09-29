<?php

declare(strict_types=1);

namespace Tests\IoEngine\Action;

use Iquety\Application\IoEngine\FileSet;
use Iquety\Application\IoEngine\Action\Input;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class InputSingleFileTest extends TestCase
{
    /** @test */
    public function singleFile(): void
    {
        $request = (new RequestFactory())->makeRequest(
            'POST',
            '/one/two',
            'x=1&y=2',
            ['name' => 'test'],
            $this->makeSingleFileStructure()
        );

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

    // /** @test */
    // public function singleFileUploadObject(): void
    // {
    //     // $request->getUploadedFiles()

    //     $request = (new RequestFactory())->makeRequest(
    //         'POST',
    //         '/one/two',
    //         'x=1&y=2',
    //         ['name' => 'test'],
    //         $this->makeSingleFileStructure()
    //     );

    //     $input = Input::fromRequest($request);


    //     $this->assertSame(['one', 'two'], $input->getPath());
    //     $this->assertSame(['one'], $input->getTarget());
    //     $this->assertSame('two', $input->param(0));
    //     $this->assertSame(1, $input->param('x'));
    //     $this->assertSame(2, $input->param('y'));
    //     $this->assertSame('test', $input->param('name'));

    //     /** @var FileSet */
    //     $fileSetOne = $input->param('inputFile');

    //     $this->assertInstanceOf(FileSet::class, $fileSetOne);
    //     $this->assertCount(1, $fileSetOne->toArray());

    //     $this->assertSame('attachment.gif', $fileSetOne->toArray()[0]->getName());
    //     $this->assertSame('image/gif', $fileSetOne->toArray()[0]->getMimeType());
    //     $this->assertSame(173, $fileSetOne->toArray()[0]->getSize());
    //     $this->assertSame(
    //         'There is no error, the file uploaded with success',
    //         $fileSetOne->toArray()[0]->getErrorMessage()
    //     );
    //     $this->assertFalse($fileSetOne->toArray()[0]->hasError());
    //     $this->assertSame(
    //         file_get_contents(__DIR__ . '/attachment.gif'),
    //         $fileSetOne->toArray()[0]->getContent()
    //     );

    //     /** @var FileSet */
    //     $fileSetTwo = $input->param('inputFile2');

    //     $this->assertInstanceOf(FileSet::class, $fileSetTwo);
    //     $this->assertCount(1, $fileSetTwo->toArray());

    //     $this->assertSame('attachment.png', $fileSetTwo->toArray()[0]->getName());
    //     $this->assertSame('image/png', $fileSetTwo->toArray()[0]->getMimeType());
    //     $this->assertSame(222, $fileSetTwo->toArray()[0]->getSize());
    //     $this->assertSame(
    //         'There is no error, the file uploaded with success',
    //         $fileSetTwo->toArray()[0]->getErrorMessage()
    //     );
    //     $this->assertFalse($fileSetTwo->toArray()[0]->hasError());
    //     $this->assertSame(
    //         file_get_contents(__DIR__ . '/attachment.png'),
    //         $fileSetTwo->toArray()[0]->getContent()
    //     );
    // }

    /**
     * Devolve uma estrutura com 1 arquivo no formato que o PHP recebe
     * os uploads enviados via HTTP
     * @return array<string,array<string,int|string>>
     */
    private function makeSingleFileStructure(): array
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
}
