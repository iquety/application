<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class NotContainsTest extends AssertionCase
{
    use HasProviderInvalidValue;
    use HasProviderFieldNotExist;

    /**
     * Recebe um valor (texto, inteiro ou decimal) transformado em texto
     * Compara com um valor (texto, inteiro ou decimal) transformado em texto
     * @return array<string,array<int,mixed>>
     */
    public function validProvider(): array
    {
        $list = [];

        $list['param int 111 not contains 112'] = $this->makeAssertionItem('param_int', 112);

        $list['param int string 222 not contains string 223'] = $this->makeAssertionItem('param_int_string', '223');

        $list['param decimal 22.5 not contains string 22.4']  = $this->makeAssertionItem('param_decimal', '22.4');
        $list['param decimal 22.5 not contains string .4']    = $this->makeAssertionItem('param_decimal', '.4');
        $list['param decimal 22.5 not contains string 21.']   = $this->makeAssertionItem('param_decimal', '21.');
        $list['param decimal 22.5 not contains string 21']    = $this->makeAssertionItem('param_decimal', '21');
        $list['param decimal 22.5 not contains string 1']     = $this->makeAssertionItem('param_decimal', '1');
        $list['param decimal 22.5 not contains decimal 22.4'] = $this->makeAssertionItem('param_decimal', 22.4);
        $list['param decimal 22.5 not contains float .5']     = $this->makeAssertionItem('param_decimal', .5);
        $list['param decimal 22.5 not contains float 21.']    = $this->makeAssertionItem('param_decimal', 21.);
        $list['param decimal 22.5 not contains int 21']       = $this->makeAssertionItem('param_decimal', 21);
        $list['param decimal 22.5 not contains int 1']        = $this->makeAssertionItem('param_decimal', 1);

        $list['param decimal 11.5 not contains string 11.4'] = $this->makeAssertionItem('param_decimal_string', '11.4');
        $list['param decimal 11.5 not contains string .4']   = $this->makeAssertionItem('param_decimal_string', '.4');
        $list['param decimal 11.5 not contains string 12.']  = $this->makeAssertionItem('param_decimal_string', '12.');
        $list['param decimal 11.5 not contains string 12']   = $this->makeAssertionItem('param_decimal_string', '12');
        $list['param decimal 11.5 not contains string 2']    = $this->makeAssertionItem('param_decimal_string', '2');
        $list['param decimal 11.5 not contains decimal 11.4'] = $this->makeAssertionItem('param_decimal_string', 11.4);
        $list['param decimal 11.5 not contains float .5']     = $this->makeAssertionItem('param_decimal_string', .5);
        $list['param decimal 11.5 not contains float 12.']    = $this->makeAssertionItem('param_decimal_string', 12.);
        $list['param decimal 11.5 not contains int 12']       = $this->makeAssertionItem('param_decimal_string', 12);
        $list['param decimal 11.5 not contains int 2']        = $this->makeAssertionItem('param_decimal_string', 2);

        $list['param string Coração!# not contains Coç']   = $this->makeAssertionItem('param_string', 'Coç');
        $list['param string Coração!# not contains rax']   = $this->makeAssertionItem('param_string', 'rax');
        $list['param string  Coração!# not contains ção#'] = $this->makeAssertionItem('param_string', 'ção#');

        $list['param boolean false not contains 0']     = $this->makeAssertionItem('param_false', '1');
        $list['param boolean false not contains int 0'] = $this->makeAssertionItem('param_false', 1);
        $list['param boolean true not contains 1']      = $this->makeAssertionItem('param_true', '0');
        $list['param boolean true not contains int 1']  = $this->makeAssertionItem('param_true', 0);

        // array [
        //    111,    // inteiro
        //    '222',  // inteiro string
        //    22.5,   // decimal
        //    '11.5', // decimal string
        //    'ção!#' // string
        // ]

        $list["array not contains integer 112"] = $this->makeAssertionItem('param_array', 112);
        $list["array not contains integer 112"] = $this->makeAssertionItem('param_array', '112');

        $list["array not contains integer string 221"] = $this->makeAssertionItem('param_array', 221);
        $list["array not contains integer string 221"] = $this->makeAssertionItem('param_array', '221');

        $list["array not contains decimal 22.4"] = $this->makeAssertionItem('param_array', 22.4);
        $list["array not contains decimal 22.4"] = $this->makeAssertionItem('param_array', '22.4');

        $list["array not contains decimal string 11.4"] = $this->makeAssertionItem('param_array', 11.4);
        $list["array not contains decimal string 11.4"] = $this->makeAssertionItem('param_array', '11.4');

        $list["array not contains string ção#"] = $this->makeAssertionItem('param_array', 'ção#');

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $list = [];

        $list['param int 111 contains int 111']    = $this->makeAssertionItem('param_int', 111);
        $list['param int 111 contains string 111'] = $this->makeAssertionItem('param_int', '111');
        $list['param int 111 contains int 11']     = $this->makeAssertionItem('param_int', 11);
        $list['param int 111 contains string 11']  = $this->makeAssertionItem('param_int', '11');
        $list['param int 111 contains int 1']      = $this->makeAssertionItem('param_int', 1);
        $list['param int 111 contains string 1']   = $this->makeAssertionItem('param_int', '1');


        $list['param int string 222 contains string 222'] = $this->makeAssertionItem('param_int_string', '222');
        $list['param int string 222 contains string 22']  = $this->makeAssertionItem('param_int_string', '22');
        $list['param int string 222 contains string 2']   = $this->makeAssertionItem('param_int_string', '2');
        $list['param int string 222 contains int 222']    = $this->makeAssertionItem('param_int_string', 222);
        $list['param int string 222 contains int 22']     = $this->makeAssertionItem('param_int_string', 22);
        $list['param int string 222 contains int 2']      = $this->makeAssertionItem('param_int_string', 2);

        $list['param decimal 22.5 contains string 22.5']  = $this->makeAssertionItem('param_decimal', '22.5');
        $list['param decimal 22.5 contains decimal 22.5'] = $this->makeAssertionItem('param_decimal', 22.5);
        $list['param decimal 22.5 contains string .5']    = $this->makeAssertionItem('param_decimal', '.5');
        // não funciona
        // $list['param decimal 22.5 contains float .5'] = $this->makeAssertionItem('param_decimal', .5);

        $list['param decimal 22.5 contains string 22.']   = $this->makeAssertionItem('param_decimal', '22.');
        // $list['param decimal 22.5 contains float 22.']    = $this->makeAssertionItem('param_decimal', 22.);
        $list['param decimal 22.5 contains string 22']    = $this->makeAssertionItem('param_decimal', '22');
        $list['param decimal 22.5 contains int 22']       = $this->makeAssertionItem('param_decimal', 22);
        $list['param decimal 22.5 contains string 2']     = $this->makeAssertionItem('param_decimal', '2');
        $list['param decimal 22.5 contains int 2']        = $this->makeAssertionItem('param_decimal', 2);

        $list['param decimal 11.5 contains string 11.5'] = $this->makeAssertionItem('param_decimal_string', '11.5');
        $list['param decimal 11.5 contains decimal 11.5'] = $this->makeAssertionItem('param_decimal_string', 11.5);
        $list['param decimal 11.5 contains string .5']    = $this->makeAssertionItem('param_decimal_string', '.5');

        // não funciona
        // $list['param decimal 11.5 contains float .5'] = $this->makeAssertionItem('param_decimal_string', .5);

        $list['param decimal 11.5 contains string 11.']   = $this->makeAssertionItem('param_decimal_string', '11.');
        // $list['param decimal 11.5 contains float 11.']    = $this->makeAssertionItem('param_decimal_string', 11.);
        $list['param decimal 11.5 contains string 11']    = $this->makeAssertionItem('param_decimal_string', '11');
        $list['param decimal 11.5 contains int 11']       = $this->makeAssertionItem('param_decimal_string', 11);
        $list['param decimal 11.5 contains string 1']     = $this->makeAssertionItem('param_decimal_string', '1');
        $list['param decimal 11.5 contains int 1']        = $this->makeAssertionItem('param_decimal_string', 1);

        $list['param string Coração!# contains Cor']   = $this->makeAssertionItem('param_string', 'Cor');
        $list['param string Coração!# contains raç']   = $this->makeAssertionItem('param_string', 'raç');
        $list['param string Coração!# contains ção!#'] = $this->makeAssertionItem('param_string', 'ção!#');

        $list['param boolean false contains 0']     = $this->makeAssertionItem('param_false', '0');
        $list['param boolean false contains int 0'] = $this->makeAssertionItem('param_false', 0);
        $list['param boolean true contains 1']      = $this->makeAssertionItem('param_true', '1');
        $list['param boolean true contains int 1']  = $this->makeAssertionItem('param_true', 1);

        // array [
        //    111,    // inteiro
        //    '222',  // inteiro string
        //    22.5,   // decimal
        //    '11.5', // decimal string
        //    'ção!#' // string
        // ]

        $list["array contains int 111"]      = $this->makeAssertionItem('param_array', 111);
        $list["array contains string 222"]   = $this->makeAssertionItem('param_array', '222');
        $list["array contains decimal 22.5"] = $this->makeAssertionItem('param_array', 22.5);
        $list["array contains string 11.5"]  = $this->makeAssertionItem('param_array', '11.5');
        $list["array contains string ção!#"] = $this->makeAssertionItem('param_array', 'ção!#');

        return $list;
    }

    /**
     * @test
     * @dataProvider validProvider
     */
    public function valueAsserted(string $paramName, mixed $valueOne): void
    {
        $input = Input::fromString(
            '/user/edit/03?' . http_build_query($this->getHttpParams())
        );

        $input->assert($paramName)->notContains($valueOne);

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
            '/user/edit/03?' . http_build_query($this->getHttpParams())
        );

        $input->assert($paramName)->notContains($valueOne);

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

        $input->assert($paramName)->notContains($valueOne);

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

        $input->assert($paramName)->notContains('xx');

        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
