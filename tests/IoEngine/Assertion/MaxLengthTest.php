<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

class MaxLengthTest extends AssertionCase
{
    use HasProviderInvalidValue;
    use HasProviderFieldNotExist;
    use HasProviderNumericValue;

    /**
     * Recebe um valor (texto, inteiro ou decimal) transformado em texto 
     * Compara com um valor (texto, inteiro ou decimal) transformado em texto
     * @return array<string,array<int,mixed>>
     */
    public function validProvider(): array
    {
        $list = [];

        $list['param int 111 has a maximum of int 111'] = $this->makeAssertionItem('param_int', 111);
        $list['param int 111 has a maximum of string 111'] = $this->makeAssertionItem('param_int', '111');

        $list['param int 111 has a maximum of int 112'] = $this->makeAssertionItem('param_int', 112);
        $list['param int 111 has a maximum of string 112'] = $this->makeAssertionItem('param_int', '112');

        $list['param int string 222 has a maximum of int 222'] = $this->makeAssertionItem('param_int_string', 222);
        $list['param int string 222 has a maximum of string 222'] = $this->makeAssertionItem('param_int_string', '222');

        $list['param int string 222 has a maximum of int 223'] = $this->makeAssertionItem('param_int_string', 223);
        $list['param int string 222 has a maximum of string 223'] = $this->makeAssertionItem('param_int_string', '223');

        $list['param decimal 22.5 has a maximum of decimal 22.5'] = $this->makeAssertionItem('param_decimal', 22.5);
        $list['param decimal 22.5 has a maximum of string 22.5'] = $this->makeAssertionItem('param_decimal', '22.5');

        $list['param decimal 22.5 has a maximum of decimal 22.6'] = $this->makeAssertionItem('param_decimal', 22.6);
        $list['param decimal 22.5 has a maximum of string 22.6'] = $this->makeAssertionItem('param_decimal', '22.6');

        $list['param string Coração!# has a maximum of 9'] = $this->makeAssertionItem('param_string', 9);
        $list['param string Coração!# has a maximum of 10'] = $this->makeAssertionItem('param_string', 10);

        $list['param boolean true has a maximum 1'] = $this->makeAssertionItem('param_true', '1');
        $list['param boolean true has a maximum int 1'] = $this->makeAssertionItem('param_true', 1);

        $list['param boolean true has a maximum 2'] = $this->makeAssertionItem('param_true', '2');
        $list['param boolean true has a maximum int 2'] = $this->makeAssertionItem('param_true', 2);

        $list['param boolean false has a maximum 0'] = $this->makeAssertionItem('param_false', '0');
        $list['param boolean false has a maximum int 0'] = $this->makeAssertionItem('param_false', 0);

        $list['param boolean false has a maximum 1'] = $this->makeAssertionItem('param_false', '1');
        $list['param boolean false has a maximum int 1'] = $this->makeAssertionItem('param_false', 1);

        $list["array has a maximum of 5"] = $this->makeAssertionItem('param_array', 5);
        $list["array has a maximum of 6"] = $this->makeAssertionItem('param_array', 6);

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $list = [];

        $list['param int 111 not has a maximum of int 110'] = $this->makeAssertionItem('param_int', 110);
        $list['param int 111 not has a maximum of string 110'] = $this->makeAssertionItem('param_int', '110');

        $list['param int string 222 not has a maximum of int 221'] = $this->makeAssertionItem('param_int_string', 221);
        $list['param int string 222 not has a maximum of string 221'] = $this->makeAssertionItem('param_int_string', '221');

        $list['param int string 222 not has a maximum of int 221'] = $this->makeAssertionItem('param_int_string', 221);
        $list['param int string 222 not has a maximum of string 221'] = $this->makeAssertionItem('param_int_string', '221');

        $list['param decimal 22.5 not has a maximum of decimal 22.4'] = $this->makeAssertionItem('param_decimal', 22.4);
        $list['param decimal 22.5 not has a maximum of decimal string 22.4'] = $this->makeAssertionItem('param_decimal', '22.4');

        $list['param decimal string 11.5 not has a maximum of decimal 11.4'] = $this->makeAssertionItem('param_decimal_string', 11.4);
        $list['param decimal string 11.5 not has a maximum of string 11.4'] = $this->makeAssertionItem('param_decimal_string', '11.4');

        $list['param string Coração!# not has a maximum of 8'] = $this->makeAssertionItem('param_string', 8);
        $list['param string Coração!# not has a maximum of 8'] = $this->makeAssertionItem('param_string', '8');

        $list['param boolean true not has a maximum 1'] = $this->makeAssertionItem('param_true', '0');
        $list['param boolean true not has a maximum int 1'] = $this->makeAssertionItem('param_true', 0);

        $list["array not has a maximum of 4"] = $this->makeAssertionItem('param_array', 4);
        $list["array not has a maximum of 4"] = $this->makeAssertionItem('param_array', '4');

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

        $input->assert($paramName)->maxLength($valueOne);

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

        $input->assert($paramName)->maxLength($valueOne);

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

        $input->assert($paramName)->maxLength($valueOne);
        
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

        $input->assert($paramName)->maxLength('xx', 'xx');
        
        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
    
    /**
     * @test
     * @dataProvider invalidNumericArgumentsProvider
     */
    public function valueIsNotNumeric(string $paramName, mixed $valueOne): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument must be numeric');

        $input = Input::fromString(
            '/user/edit/03?' . http_build_query([
                'param_string' => 'text',
                'param_array' => ['one', 'two'],
            ]),
        );

        $input->assert($paramName)->maxLength($valueOne);
        
        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
