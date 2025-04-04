<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

class IsBrPhoneNumberTest extends AssertionCase
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
            'param_0300_spaces' => "0300 313 4701",
            'param_0500_spaces' => "0500 313 4701",
            'param_0800_spaces' => "0800 729 0722",
            'param_0900_spaces' => "0900 313 4701",
    
            'param_0300_dashs' => "0300-313-4701",
            'param_0500_dashs' => "0500-313-4701",
            'param_0800_dashs' => "0800-729-0722",
            'param_0900_dashs' => "0900-313-4701",
    
            'param_3003_spaces' => "3003 3030",
            'param_4003_spaces' => "4003 3030",
            'param_4004_spaces' => "4004 3030",
    
            'param_3003_dash' => "3003-3030",
            'param_4003_dash' => "4003-3030",
            'param_4004_dash' => "4004-3030",
    
            'param_3003_int' => 30033030,
            'param_4003_int' => 40033030,
            'param_4004_int' => 40043030,

            // movel
            'param_mobile' => "(87) 9985-0997",
            'param_mobile_dashes' => "87-9985-0997",
            'param_mobile_digits' => "8799850997",
            'param_mobile_spaces' => "87 9985 0997",
            'param_mobile_int' => 8799850997,
    
            // movel SP
            'param_mobile_prefix_9' => "(11) 9 9985-0997",
            'param_mobile_prefix_9_dashes' => "11-9-9985-0997",
            'param_mobile_prefix_9_digits' => "11999850997",
            'param_mobile_prefix_9_spaces' => "11 9 9985 0997",
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
            'param_0300_dots' => "0300.313.4701",
            'param_0500_dots' => "0500.313.4701",
            'param_0800_dots' => "0800.729.0722",
            'param_0900_dots' => "0900.313.4701",

            'param_3003_dots' => "3003.3030",
            'param_4003_dots' => "4003.3030",
            'param_4004_dots' => "4004.3030",

            // movel
            'param_mobile'        => "(87).9985-0997",
            'param_mobile_digits' => "87.9985.0997",

            // movel SP
            'param_mobile_prefix_9'        => "(11) 9.9985-0997",
            'param_mobile_prefix_9_digits' => "11.9.9985.0997",

            'param_invalid_phone_7_chars'  => '1234567',
            'param_invalid_phone_9_chars'  => '123456789',
            'param_invalid_phone_12_chars' => '123456789012',

            'param_invalid_phone_7_digits'  => '1234-567',
            'param_invalid_phone_9_digits'  => '1234-56789',
            'param_invalid_phone_12_digits' => '12 345678-9012',

            'param_invalid_phone_invalid_characters' => '12A45-678',
            'param_invalid_phone_empty_string'       => '',
            'param_invalid_phone_special_characters' => '123@5-678',

            'param_empty_string'      => '',
            'param_one_space_string'  => ' ',
            'param_two_spaces_string' => '  ',
            'param_array'             => ['a'],
            'param_false'             => false,
            'param_true'              => true,
            'param_string_false'      => 'false',
            'param_string_true'       => 'true',
            'integer'                 => 12345,
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

        $input->assert($paramName)->isBrPhoneNumber();

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

        $input->assert($paramName)->isBrPhoneNumber();

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

        $input->assert($paramName)->isBrPhoneNumber();
        
        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
