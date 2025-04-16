<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ContainsTest extends AssertionCase
{
    use HasProviderInvalidValue;
    use HasProviderFieldNotExist;

    /**
     * A aplicação recebe os valores da requisição
     * Recebe um valor (texto, inteiro, decimal ou array) transformado em texto
     * Compara com um valor (texto, inteiro ou decimal) transformado em texto
     * @return array<string,array<int,mixed>>
     */
    public function validProvider(): array
    {
        $list = [];

        $list['string 222 contains string 222'] = $this->makeAssertionItem('param_int_string', '222');
        $list['string 11.5 contains string .5'] = $this->makeAssertionItem('param_decimal_string', '.5');
        $list['string Coração!# contains raç']  = $this->makeAssertionItem('param_string', 'raç');

        // array [
        //    111,    // inteiro
        //    '222',  // inteiro string
        //    22.5,   // decimal
        //    '11.5', // decimal string
        //    'ção!#' // string
        // ]

        $list["array contains string 222"]   = $this->makeAssertionItem('param_array', '222');
        $list["array contains string 11.5"]  = $this->makeAssertionItem('param_array', '11.5');
        $list["array contains string ção!#"] = $this->makeAssertionItem('param_array', 'ção!#');

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $list = [];

        $list['string 222 not contains string 223'] = $this->makeAssertionItem('param_int_string', '223');
        $list['string 11.5 not contains string 11.4'] = $this->makeAssertionItem('param_decimal_string', '11.4');
        $list['string Coração!# not contains Coç']   = $this->makeAssertionItem('param_string', 'Coç');

        // array [
        //    111,    // inteiro
        //    '222',  // inteiro string
        //    22.5,   // decimal
        //    '11.5', // decimal string
        //    'ção!#' // string
        // ]

        $list["array not contains int 112"]             = $this->makeAssertionItem('param_array', 112);
        $list["array not contains int string 112"]      = $this->makeAssertionItem('param_array', '112');
        $list["array not contains int 221"]             = $this->makeAssertionItem('param_array', 221);
        $list["array not contains int string 221"]      = $this->makeAssertionItem('param_array', '221');
        $list["array not contains decimal 22.4"]        = $this->makeAssertionItem('param_array', 22.4);
        $list["array not contains decimal string 22.4"] = $this->makeAssertionItem('param_array', '22.4');
        $list["array not contains decimal 11.4"]        = $this->makeAssertionItem('param_array', 11.4);
        $list["array not contains decimal string 11.4"] = $this->makeAssertionItem('param_array', '11.4');
        $list["array not contains string ção#"]         = $this->makeAssertionItem('param_array', 'ção#');

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

        $input->assert($paramName)->contains($valueOne);

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
     */
    public function valueNotAsserted(string $paramName, mixed $valueOne): void
    {
        $this->expectException(AssertionResponseException::class);
        $this->expectExceptionMessage('The value was not successfully asserted');

        $input = Input::fromString(
            '/user/edit/03?' . http_build_query($this->getHttpParams()),
        );

        $input->assert($paramName)->contains($valueOne);

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

        $input->assert($paramName)->contains($valueOne);

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

        $input->assert($paramName)->contains('xx');

        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
