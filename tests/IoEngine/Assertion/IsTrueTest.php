<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class IsTrueTest extends AssertionCase
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
            'boolean_true'       => true,
            'string_true'        => 'true',
            'binary_true'        => 1,
            'string_binary_true' => '1',
            'string_on'          => 'on',
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
            'boolean_false'       => false,
            'string_false'        => 'false',
            'binary_true'         => 0,
            'string_binary_false' => '0',
            'empty_string'        => '',
            'one_space_string'    => ' ',
            'two_spaces_string'   => '  ',
            'string'              => 'x',
            // 'empty_array'         => [],
            'off'                 => 'off'
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

        $input->assert($paramName)->isTrue();

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

        $input->assert($paramName)->isTrue();

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

        $input->assert($paramName)->isTrue();

        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
