<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

class IsCepTest extends AssertionCase
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
            'param_format_1' => '12345-678',
            'param_format_2' => '98765-432',
            'param_format_3' => '01000-000',
            'param_format_4' => '99999-999',
            'param_format_5' => 12345678,
            'param_format_6' => 98765432,
            'param_format_7' => 11000000,
            'param_format_8' => 99999999,
            'param_format_9' => '99999-999'
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
            'param_invalid_cep_too_short'          => '1234-567',
            'param_invalid_cep_too_long'           => '123456-789',
            'param_invalid_cep_invalid_characters' => '12A45-678',
            'param_invalid_cep_empty_string'       => '',
            'param_invalid_cep_spaces'             => '123 45-678',
            'param_invalid_cep_special_characters' => '123@5-678',
            'param_invalid_cep_many_numbers'       => 123567890,
            'param_invalid_cep_loss_numbers'       => 123567,
            'param_empty_string'                   => '',
            'param_one_space_string'               => ' ',
            'param_two_spaces_string'              => '  ',
            'param_decimal'                        => 123.456,
            'param_array'                          => ['a'],
            'param_false'                          => false,
            'param_true'                           => true,
            'param_string_false'                   => 'false',
            'param_string_true'                    => 'true',
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

        $input->assert($paramName)->isCep();

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

        $input->assert($paramName)->isCep();

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

        $input->assert($paramName)->isCep();
        
        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
