<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class GreaterThanTest extends AssertionCase
{
    use HasProviderInvalidValue;
    use HasProviderFieldNotExist;
    use HasProviderNonNumericValue;

    /**
     * Recebe um valor (texto, inteiro ou decimal) transformado em texto
     * Compara com um valor (texto, inteiro ou decimal) transformado em texto
     * @return array<string,array<int,mixed>>
     */
    public function validProvider(): array
    {
        $list = [];

        // true é transformado em 1
        $list['param true greater than 0'] = $this->makeAssertionItem('param_true', 0);

        $list['param int 111 greater than int 110'] = $this->makeAssertionItem(
            'param_int',
            110
        );

        $list['param int 111 greater than string 110'] = $this->makeAssertionItem(
            'param_int',
            '110'
        );

        $list['param string 222 greater than int 221'] = $this->makeAssertionItem(
            'param_int_string',
            221
        );

        $list['param string 222 greater than string 221'] = $this->makeAssertionItem(
            'param_int_string',
            '221'
        );

        $list['param decimal 22.5 greater than decimal 22.4'] = $this->makeAssertionItem(
            'param_decimal',
            22.4
        );

        $list['param decimal 22.5 greater than string 22.4'] = $this->makeAssertionItem(
            'param_decimal',
            '22.4'
        );

        $list['param decimal 22.0 greater than decimal 21.9'] = $this->makeAssertionItem(
            'param_decimal_zero',
            21.9
        );

        $list['param decimal 22.0 greater than string 21.9'] = $this->makeAssertionItem(
            'param_decimal_zero',
            '21.9'
        );

        $list['param decimal string 11.5 greater than decimal 11.4'] = $this->makeAssertionItem(
            'param_decimal_string',
            11.4
        );

        $list['param decimal string 11.5 greater than string 11.4'] = $this->makeAssertionItem(
            'param_decimal_string',
            '11.4'
        );


        $list["array greater than 4"] = $this->makeAssertionItem('param_array', 4);

        return $list;
    }

    /**
     * @test
     * @dataProvider validProvider
     */
    public function valueAsserted(string $paramName, mixed $valueOne): void
    {
        $input = Input::fromString(
            '/user/edit/03?' . http_build_query($this->getHttpParams()),
        );

        $input->assert($paramName)->greaterThan($valueOne);

        // se a asserção não passar, uma exceção será lançada
        $input->validOrResponse();

        // se chegar até aqui... tudo correu bem
        $this->assertTrue(true);
    }

    /**
     * @return array<string,array<int,mixed>>
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function invalidProvider(): array
    {
        $list = [];

        // string é inválido
        $list['param string not greater than 0'] = $this->makeAssertionItem('param_string', 0);

        // false é transformado em 0
        $list['param false not greater than 0'] = $this->makeAssertionItem('param_false', 0);

        $list['param int 111 not greater than int 111'] = $this->makeAssertionItem(
            'param_int',
            111
        );

        $list['param int 111 not greater than string 111'] = $this->makeAssertionItem(
            'param_int',
            '111'
        );

        $list['param int 111 not greater than int 112'] = $this->makeAssertionItem(
            'param_int',
            112
        );

        $list['param int 111 not greater than string 112'] = $this->makeAssertionItem(
            'param_int',
            '112'
        );

        $list['param string 222 not greater than int 222'] = $this->makeAssertionItem(
            'param_int_string',
            222
        );

        $list['param string 222 not greater than string 222'] = $this->makeAssertionItem(
            'param_int_string',
            '222'
        );

        $list['param string 222 not greater than int 223'] = $this->makeAssertionItem(
            'param_int_string',
            223
        );

        $list['param string 222 not greater than string 223'] = $this->makeAssertionItem(
            'param_int_string',
            '223'
        );

        $list['param decimal 22.5 not greater than decimal 22.5'] = $this->makeAssertionItem(
            'param_decimal',
            22.5
        );

        $list['param decimal 22.5 not greater than string 22.5'] = $this->makeAssertionItem(
            'param_decimal',
            '22.5'
        );

        $list['param decimal 22.5 not greater than decimal 22.6'] = $this->makeAssertionItem(
            'param_decimal',
            22.6
        );

        $list['param decimal 22.5 not greater than string 22.6'] = $this->makeAssertionItem(
            'param_decimal',
            '22.6'
        );

        $list['param decimal 22.0 not greater than decimal 22.0'] = $this->makeAssertionItem(
            'param_decimal_zero',
            22.0
        );

        $list['param decimal 22.0 not greater than string 22.0'] = $this->makeAssertionItem(
            'param_decimal_zero',
            '22.0'
        );

        $list['param decimal 22.0 not greater than decimal 22.1'] = $this->makeAssertionItem(
            'param_decimal_zero',
            22.1
        );

        $list['param decimal 22.0 not greater than string 22.1'] = $this->makeAssertionItem(
            'param_decimal_zero',
            '22.1'
        );

        $list['param decimal string 11.5 not greater than decimal 11.5'] = $this->makeAssertionItem(
            'param_decimal_string',
            11.5
        );

        $list['param decimal string 11.5 not greater than string 11.5'] = $this->makeAssertionItem(
            'param_decimal_string',
            '11.5'
        );

        $list['param decimal string 11.5 not greater than decimal 11.6'] = $this->makeAssertionItem(
            'param_decimal_string',
            11.6
        );

        $list['param decimal string 11.5 not greater than string 11.6'] = $this->makeAssertionItem(
            'param_decimal_string',
            '11.6'
        );


        $list["array not greater than 5"] = $this->makeAssertionItem('param_array', 5);

        return $list;
    }

    /**
     * Recebe um valor (texto, inteiro ou decimal) transformado em texto
     * Compara com um valor (texto, inteiro ou decimal) transformado em texto
     * @test
     * @dataProvider invalidProvider
     */
    public function valueNotAsserted(string $paramName, mixed $valueOne): void
    {
        $this->expectException(AssertionResponseException::class);
        $this->expectExceptionMessage('The value was not successfully asserted');

        $input = Input::fromString(
            '/user/edit/03?' . http_build_query($this->getHttpParams()),
        );

        $input->assert($paramName)->greaterThan($valueOne);

        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }

    /**
     * @test
     * @dataProvider invalidNonNumericArgumentsProvider
     */
    public function valueIsInvalidNonNumeric(string $paramName, mixed $valueOne): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument must be numeric');

        $input = Input::fromString(
            '/user/edit/03?' . http_build_query($this->getHttpParams()),
        );

        $input->assert($paramName)->greaterThan($valueOne);

        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }

    /**
     * @test
     * @dataProvider invalidObjectArgumentsProvider
     */
    public function valueIsInvalidObject(string $paramName, mixed $valueOne): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument is not valid');

        $input = Input::fromString('/user/edit/03?' . http_build_query([
            'param_string' => 'text',
            'param_int'    => 123,
            'param_float'  => 12.3,
            'param_array'  => ['one', 'two'],
        ]));

        $input->assert($paramName)->greaterThan($valueOne);

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

        $input->assert($paramName)->greaterThan(1);

        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
