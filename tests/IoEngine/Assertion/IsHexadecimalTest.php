<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class IsHexadecimalTest extends AssertionCase
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
            'hexadecimal_1'  => '1234567890abcdef',
            'hexadecimal_2'  => 'ABCDEF0123456789',
            'hexadecimal_3'  => '0123456789abcdefABCDEF',
            'hexadecimal_4'  => '0123456789ABCDEFabcdef',
            'hexadecimal_5'  => '1234567890ABCDEFabcdef0',
            'hexadecimal_6'  => '0123456789abcdefABCDEF0',
            'hexadecimal_7'  => '1234567890ABCDEFabcdef0123456789abcdef',
            'hexadecimal_8'  => '0123456789abcdefABCDEF0123456789abcdef',
            'hexadecimal_9'  => '1234567890ABCDEFabcdef0123456789ABCDEF',
            'hexadecimal_10' => '0123456789abcdefABCDEF0123456789ABCDEF',
        ];

        $list = [];

        foreach (array_keys($httpParams) as $param) {
            $label = $this->paramToLabel($param);

            $list[$label] = $this->makeAssertionItem($param, $httpParams);
        }

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $httpParams = [
            'invalid_hexadecimal_1'  => '1234567890g',
            'invalid_hexadecimal_2'  => 'ABCDEF012345678G',
            'invalid_hexadecimal_3'  => '0123456789abcdefG',
            'invalid_hexadecimal_4'  => '0123456789ABCDEFg',
            'invalid_hexadecimal_5'  => '1234567890ABCDEFabcdefg',
            'invalid_hexadecimal_6'  => '0123456789abcdefABCDEFg',
            'invalid_hexadecimal_7'  => '1234567890ABCDEFabcdefg123456789abcdef',
            'invalid_hexadecimal_8'  => '0123456789abcdefABCDEFg123456789abcdef',
            'invalid_hexadecimal_9'  => '1234567890ABCDEFabcdefg123456789ABCDEF',
            'invalid_hexadecimal_10' => '0123456789abcdefABCDEFg123456789ABCDEF',
            'empty_string'           => '',
            'one_space_string'       => ' ',
            'two_spaces_string'      => '  ',
            'array'                  => ['a'],
            // 'false'                  => false,                                      // false é mudado para 0
            // 'true'                   => true,                                       // false é mudado para 1
            // 'integer'                => 12345,
        ];

        $list = [];

        foreach (array_keys($httpParams) as $param) {
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

        $input->assert($paramName)->isHexadecimal();

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

        $input->assert($paramName)->isHexadecimal();

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

        $input->assert($paramName)->isHexadecimal();

        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
