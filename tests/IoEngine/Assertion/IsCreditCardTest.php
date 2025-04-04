<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

class IsCreditCardTest extends AssertionCase
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
            'visa'             => '4111111111111111',
            'mastercard'       => '5500000000000004',
            'american_express' => '340000000000009',
            'diners_club'      => '30000000000004',
            'discover'         => '6011000000000004',
            'jcb'              => '3088000000000009',
    
            'visa_numeric'             => 4111111111111111,
            'mastercard_numeric'       => 5500000000000004,
            'american_express_numeric' => 340000000000009,
            'diners_club_numeric'      => 30000000000004,
            'discover_numeric'         => 6011000000000004,
            'jcb_numeric'              => 3088000000000009,
    
            'visa_with_signals'             => '4111-1111-1111-1111',
            'mastercard_with_signals'       => '5500-0000-0000-0004',
            'american_express_with_signals' => '3400-000000-00009',
            'diners_club_with_signals'      => '3000-000000-0004',
            'discover_with_signals'         => '6011-0000-0000-0004',
            'jcb_with_signals'              => '3088-0000-0000-0009',
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
            'random_number'      => '1234567890123456',
            'too_short'          => '4111111111111',
            'too_long'           => '55000000000000000000',
            'non_numeric'        => 'abcdefg',
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

        $input->assert($paramName)->isCreditCard();

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

        $input->assert($paramName)->isCreditCard();

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

        $input->assert($paramName)->isCreditCard();
        
        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
