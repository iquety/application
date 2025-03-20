<?php

declare(strict_types=1);

namespace Tests\IoEngine\Action;

use Iquety\Application\IoEngine\Action\Input;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class InputTypesTest extends TestCase
{
    // fromConsoleArguments não resolve os tipos dos argumentos de terminal

    /** @test */
    public function fromString(): void
    {
        $queryString = [
            'one=01', // numerico
            'two=true', // booleano
            'three=null', // null
            'four=abc', // string,
            'five=1.3', // decimal
            'six[]=true', // booleano
            'six[]=null', // null
            'six[]=abc', // string,
            'six[]=1.3', // decimal
        ];

        $input = Input::fromString(
            '/user/edit/03?' . implode('&', $queryString)
        );

        $this->assertSame('GET', $input->getMethod());
        $this->assertSame('user/edit/03', $input->getPathString());
        $this->assertSame(['user', 'edit', '03'], $input->getPath());
        $this->assertSame(['user'], $input->getTarget()); // nó atual
        $this->assertTrue($input->hasNext()); // edit é o próximo nó

        // próximos nós são considerados parâmetros
        $this->assertSame('edit', $input->param(0));
        $this->assertSame(3, $input->param(1));

        $this->assertSame([
            0 => 'edit',
            1 => 3,
            'one' => 1,
            'two' => true,
            'three' => null,
            'four' => 'abc',
            'five' => 1.3,
            'six' => [
                true, // booleano
                null, // null
                'abc', // string,
                1.3, // decimal
            ]
        ], $input->toArray());
    }

    /** @test */
    public function fromRequest(): void
    {
        $queryString = [
            'one=01', // numerico
            'two=true', // booleano
            'three=null', // null
            'four=abc', // string,
            'five=1.3', // decimal
            'six[]=true', // booleano
            'six[]=null', // null
            'six[]=abc', // string,
            'six[]=1.3', // decimal
        ];

        $postParams = [
            'seven' => true,
            'eight' => null,
            'nine' => 'abc',
            'ten' => 1.3
        ];

        $request = (new RequestFactory())->makeRequest(
            'POST',
            '/user/edit/03',
            implode('&', $queryString),
            $postParams,
            $this->phpSingleFile()
        );

        $input = Input::fromRequest($request);

        $this->assertSame('POST', $input->getMethod());
        $this->assertSame('user/edit/03', $input->getPathString());
        $this->assertSame(['user', 'edit', '03'], $input->getPath());
        $this->assertSame(['user'], $input->getTarget()); // nó atual
        $this->assertTrue($input->hasNext()); // edit é o próximo nó

        // próximos nós são considerados parâmetros
        $this->assertSame('edit', $input->param(0));
        $this->assertSame(3, $input->param(1));

        $this->assertSame([
            0 => 'edit',
            1 => 3,
            'one' => 1,
            'two' => true,
            'three' => null,
            'four' => 'abc',
            'five' => 1.3,
            'six' => [
                true, // booleano
                null, // null
                'abc', // string,
                1.3, // decimal
            ],
            'seven' => true,
            'eight' => null,
            'nine' => 'abc',
            'ten' => 1.3,
            'inputFile' => $input->toArray()['inputFile']
        ], $input->toArray());
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
            ]
        ];
    }
}
