<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

class IsCpfTest extends AssertionCase
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
            'valid_cpf_1' => '187.260.788-80',
            'valid_cpf_2' => '254.659.882-15',
            'valid_cpf_3' => '153.347.537-70',
            'valid_cpf_4' => '18726078880',
            'valid_cpf_5' => '25465988215',
            'valid_cpf_6' => '15334753770',
            'valid_cpf_7' => 18726078880,
            'valid_cpf_8' => 25465988215,
            'valid_cpf_9' => 15334753770
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
            'invalid_cpf_0' => '00000000000',
            'invalid_cpf_1' => '11111111111',
            'invalid_cpf_2' => '22222222222',
            'invalid_cpf_3' => '33333333333',
            'invalid_cpf_4' => '44444444444',
            'invalid_cpf_5' => '55555555555',
            'invalid_cpf_6' => '66666666666',
            'invalid_cpf_7' => '77777777777',
            'invalid_cpf_8' => '88888888888',
            'invalid_cpf_9' => '99999999999',

            'invalid_integer_cpf_0' => 00000000000,
            'invalid_integer_cpf_1' => 11111111111,
            'invalid_integer_cpf_2' => 22222222222,
            'invalid_integer_cpf_3' => 33333333333,
            'invalid_integer_cpf_4' => 44444444444,
            'invalid_integer_cpf_5' => 55555555555,
            'invalid_integer_cpf_6' => 66666666666,
            'invalid_integer_cpf_7' => 77777777777,
            'invalid_integer_cpf_8' => 88888888888,
            'invalid_integer_cpf_9' => 99999999999,

            'invalid_cpf_0_signals' => '000.000.000-00',
            'invalid_cpf_1_signals' => '111.111.111-11',
            'invalid_cpf_2_signals' => '222.222.222-22',
            'invalid_cpf_3_signals' => '333.333.333-33',
            'invalid_cpf_4_signals' => '444.444.444-44',
            'invalid_cpf_5_signals' => '555.555.555-55',
            'invalid_cpf_6_signals' => '666.666.666-66',
            'invalid_cpf_7_signals' => '777.777.777-77',
            'invalid_cpf_8_signals' => '888.888.888-88',
            'invalid_cpf_9_signals' => '999.999.999-99',

            'invalid_cpf_1_calc' => '17734532493',
            'invalid_cpf_2_calc' => '00135829304',
            'invalid_cpf_3_calc' => '12070275460',
            'invalid_cpf_4_calc' => '00138625504',
            'invalid_cpf_5_calc' => '00127436714',
            'invalid_cpf_6_calc' => '00136123694',
            'invalid_cpf_7_calc' => '13090940977',
            'invalid_cpf_8_calc' => '01303816444',
            'invalid_cpf_9_calc' => '00704535034',

            'invalid_cpf_integer_1_calc' => 17734532493,
            'invalid_cpf_integer_2_calc' => 10135829304,
            'invalid_cpf_integer_3_calc' => 12070275460,
            'invalid_cpf_integer_4_calc' => 10138625504,
            'invalid_cpf_integer_5_calc' => 10127436714,
            'invalid_cpf_integer_6_calc' => 10136123694,
            'invalid_cpf_integer_7_calc' => 13090940977,
            'invalid_cpf_integer_8_calc' => 11303816444,
            'invalid_cpf_integer_9_calc' => 10704535034,

            'empty_string'       => '',
            'one_space_string'   => ' ',
            'two_spaces_string'  => '  ',
            'array'              => ['a'],
            'param_false'        => false,
            'param_true'         => true,
            'param_string_false' => 'false',
            'param_string_true'  => 'true',
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

        $input->assert($paramName)->isCpf();

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

        $input->assert($paramName)->isCpf();

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

        $input->assert($paramName)->isCpf();
        
        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
