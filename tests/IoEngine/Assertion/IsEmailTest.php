<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class IsEmailTest extends AssertionCase
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
            'valid' => 'teste@teste.com',
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
            'invalid'            => 'testeteste.com',
            'empty_string'       => '',
            'one_space_string'   => ' ',
            'two_spaces_string'  => '  ',
            'array'              => ['a'],
            'param_false'        => false,
            'param_true'         => true,
            'param_string_false' => 'false',
            'param_string_true'  => 'true',
            'integer'            => 12345,
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

        $input->assert($paramName)->isEmail();

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

        $input->assert($paramName)->isEmail();

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

        $input->assert($paramName)->isEmail();

        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
