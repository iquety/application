<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

class IsHexColorTest extends AssertionCase
{
    use HasProviderFieldNotExist;

    /**
     * Recebe um valor (texto, inteiro ou decimal) transformado em texto 
     * Compara com um valor (texto, inteiro ou decimal) transformado em texto
     * @return array<string,array<int,mixed>>
     */
    public function validProvider(): array
    {
        $httpParams = [
            'hexcolor_1' => '#123456',
            'hexcolor_2' => '#ABCDEF',
            'hexcolor_3' => '#012345',
            'hexcolor_4' => '#FEDCBA',
            'hexcolor_5' => '#987654',
            'hexcolor_6' => '#3210AB',
            'hexcolor_7' => '#CDEF01',

            'hexcolor_alpha_1' => '#12345600',
            'hexcolor_alpha_2' => '#ABCDEFaa',
            'hexcolor_alpha_3' => '#012345bb',
            'hexcolor_alpha_4' => '#FEDCBAcc',
            'hexcolor_alpha_5' => '#987654dd',
            'hexcolor_alpha_6' => '#3210ABee',
            'hexcolor_alpha_7' => '#CDEF01ff',
        ];

        $list = [];
        
        foreach(array_keys($httpParams) as $param) {
            $label = $this->paramToLabel($param);

            $list[$label] = $this->makeAssertionItem($param, $httpParams);
        }

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $httpParams = [
            'invalid_hexcolor_1'  => '#12345G',
            'invalid_hexcolor_2'  => '#ABCDEFG',
            'invalid_hexcolor_3'  => '#0123456',
            'invalid_hexcolor_4'  => '#ABCDEF0',
            'invalid_hexcolor_5'  => '#1234567',
            'invalid_hexcolor_8'  => '#ABCDEF012',
            'invalid_hexcolor_9'  => '#123456789',
            'invalid_hexcolor_10' => '#ABCDEF0123',
            'invalid_hexcolor_11' => '#123456789A',
            'invalid_hexcolor_12' => '#ABCDEF01234',
            'invalid_hexcolor_13' => '#123456789AB',
            'invalid_hexcolor_14' => '#ABCDEF012345',
            'invalid_hexcolor_15' => '#123456789ABC',
            'invalid_hexcolor_16' => '#ABCDEF0123456',
            'invalid_hexcolor_17' => '#123456789ABCD',
            'invalid_hexcolor_18' => '#ABCDEF01234567',
            'invalid_hexcolor_19' => '#123456789ABCDEF',
            'invalid_hexcolor_20' => '#ABCDEF012345678',
            'empty_string'        => '',
            'one_space_string'    => ' ',
            'two_spaces_string'   => '  ',
            'array'               => ['a'],
            'false'               => false,                // false é mudado para 0
            'true'                => true,                 // false é mudado para 1
            'integer'             => 12345,
        ];

        $list = [];

        foreach(array_keys($httpParams) as $param) {
            $label = $this->paramToLabel($param);

            $list[$label] = $this->makeAssertionItem($param, $httpParams);
        }

        return $list;
    }

    /**
     * @test
     * @dataProvider validProvider
     * @param array<string,array<int,mixed>> $httpParams
     */
    public function valueAsserted(string $paramName, array $httpParams): void
    {
        $input = Input::fromString(
            '/user/edit/03?' . http_build_query($httpParams),
        );

        $input->assert($paramName)->isHexColor();

        // se a asserção não passar, uma exceção será lançada
        $input->validOrResponse();

        // se chegar até aqui... tudo correu bem
        $this->assertTrue(true);
    }

    /**
     * Recebe um valor (texto, inteiro ou decimal) transformado em texto 
     * Compara com um valor (texto, inteiro ou decimal) transformado em texto
     * @test
     * @dataProvider invalidProvider
     * @param array<string,array<int,mixed>> $httpParams
     */
    public function valueNotAsserted(string $paramName, array $httpParams): void
    {
        $this->expectException(AssertionResponseException::class);
        $this->expectExceptionMessage('The value was not successfully asserted');

        $input = Input::fromString(
            '/user/edit/03?' . http_build_query($httpParams),
        );

        $input->assert($paramName)->isHexColor();

        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }

    /**
     * @test
     * @dataProvider invalidFieldExistsProvider
     */
    public function fieldDoesNotExist(string $paramName): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Field '$paramName' does not exist");

        $input = Input::fromString(
            '/user/edit/03?' . http_build_query(['param_null' => null]),
        );

        $input->assert($paramName)->isHexColor();
        
        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
