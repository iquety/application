<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

class IsAlphaTest extends AssertionCase
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
            'param_text_1'  => 'TEXTO',
            'param_text_2'  => 'abc',
            'param_text_3'  => 'xyz',
            'param_text_4'  => 'TextoABC',
            'param_text_5'  => 'XYZTexto',
            'param_text_6'  => 'TextoXYZ',
            'param_text_7'  => 'TextoABC',
            'param_text_8'  => 'abcxyz',
            'param_text_9'  => 'AbCxYz',
            'param_text_10' => 'texto',
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
            'param_iso_8601_dirty'                 => '00002024-12-31xxx',
            'param_european_format_dirty'          => '31/12//2024',
            'param_us_format_dirty'                => 'xxx12/31/2024',
            'param_alternative_format_dirty'       => 'rr2x024.12.31',
            'param_abbreviated_month_name_dirty'   => 'xxx31-Dec-2024',
            'param_full_month_name_dirty'          => 'xxxDecember 31, 2024',
            'param_iso_8601_invalid_month'         => '2024-13-31',
            'param_iso_8601_invalid_day'           => '2024-12-32',
            'param_european_format_month'          => '31/13/2024',
            'param_european_format_day'            => '32/12/2024',
            'param_us_format_month'                => '13/31/2024',
            'param_us_format_day'                  => '12/32/2024',
            'param_alternative_format_month'       => '2024.13.31',
            'param_alternative_format_day'         => '2024.12.32',
            'param_abbreviated_month_name_month'   => '31-Err-2024',
            'param_abbreviated_month_name_day'     => '32-Dec-2024',
            'param_full_month_name_month'          => 'Invalid 31, 2024',
            'param_full_month_name_day'            => 'December 32, 2024',
            'param_special_characters'             => '@#$%^&*()',
            'param_numbers_and_special_characters' => '123@#$%',
            'param_empty_string'                   => '',
            'param_one_space_string'               => ' ',
            'param_two_spaces_string'              => '  ',
            'param_integer'                        => 123456,
            'param_decimal'                        => 123.456,
            'param_array'                          => ['a'],
            'param_false'                          => false,
            'param_true'                           => true,
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

        $input->assert($paramName)->isAlpha();

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

        $input->assert($paramName)->isAlpha();

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

        $input->assert($paramName)->isAlpha();
        
        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
