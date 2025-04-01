<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

class NotMatchesTest extends AssertionCase
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
        
        $list['param int 111 not matches 112'] = $this->makeAssertionItem('param_int', '/112/');
        $list['param int 111 not matches 12']  = $this->makeAssertionItem('param_int', '/12/');
        $list['param int 111 not matches 2']   = $this->makeAssertionItem('param_int', '/2/');

        $list['param int string 222 not matches 223'] = $this->makeAssertionItem('param_int_string', '/223/');
        $list['param int string 222 not matches 23']  = $this->makeAssertionItem('param_int_string', '/23/');
        $list['param int string 222 not matches 3']   = $this->makeAssertionItem('param_int_string', '/3/');

        $list['param decimal 22.5 not matches 22.5'] = $this->makeAssertionItem('param_decimal', '/22\.6/');
        $list['param decimal 22.5 not matches 23\.'] = $this->makeAssertionItem('param_decimal', '/23\./');
        $list['param decimal 22.5 not matches 23']   = $this->makeAssertionItem('param_decimal', '/23/');
        $list['param decimal 22.5 not matches .6']   = $this->makeAssertionItem('param_decimal', '/\.6/');
        $list['param decimal 22.5 not matches 6']    = $this->makeAssertionItem('param_decimal', '/6/');

        $list['param decimal 11.5 not matches 12\.6'] = $this->makeAssertionItem('param_decimal_string', '/11\.6/');
        $list['param decimal 11.5 not matches 12\.']  = $this->makeAssertionItem('param_decimal_string', '/12\./');
        $list['param decimal 11.5 not matches 12']    = $this->makeAssertionItem('param_decimal_string', '/12/');
        $list['param decimal 11.5 not matches \.6']   = $this->makeAssertionItem('param_decimal_string', '/\.6/');
        $list['param decimal 11.5 not matches 6']     = $this->makeAssertionItem('param_decimal_string', '/6/');

        $list['param string Coração!# not matches Cr']          = $this->makeAssertionItem('param_string', '/Cr/');
        $list['param string Coração!# not matches rç']          = $this->makeAssertionItem('param_string', '/rç/');
        $list['param string Coração!# not matches ço!#']        = $this->makeAssertionItem('param_string', '/ço!#/');
        $list['param string Coração!# not matches [a-z]*%']     = $this->makeAssertionItem('param_string', '/[a-z]*%/');
        $list['param string Coração!# not matches [a-zçã!#]*%'] = $this->makeAssertionItem('param_string', '/[a-zçã!#]*%/');

        $list['param boolean true not matches [0-9]{2}']  = $this->makeAssertionItem('param_true', '/[0-9]{2}/');
        $list['param boolean true not matches 1']         = $this->makeAssertionItem('param_true', '/0/');
        $list['param boolean false not matches [0-9]{2}'] = $this->makeAssertionItem('param_false', '/[0-9]{2}/');
        $list['param boolean false not matches 0']        = $this->makeAssertionItem('param_false', '/1/');

        $list["array not matches 12"]          = $this->makeAssertionItem('param_array', "/12/");
        $list["array not matches 23"]          = $this->makeAssertionItem('param_array', "/23/");
        $list["array not matches \.6"]         = $this->makeAssertionItem('param_array', "/\.6/");
        $list["array not matches 1\.6"]        = $this->makeAssertionItem('param_array', "/1\.6/");
        $list["array not matches [a-zçã!#]*%"] = $this->makeAssertionItem('param_array', "/[a-zçã!#]*%/");

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $list = [];

        $list['param int 111 matches 111'] = $this->makeAssertionItem('param_int', '/111/');
        $list['param int 111 matches 11']  = $this->makeAssertionItem('param_int', '/11/');
        $list['param int 111 matches 1']   = $this->makeAssertionItem('param_int', '/1/');

        $list['param int string 222 matches 222'] = $this->makeAssertionItem('param_int_string', '/222/');
        $list['param int string 222 matches 22']  = $this->makeAssertionItem('param_int_string', '/22/');
        $list['param int string 222 matches 2']   = $this->makeAssertionItem('param_int_string', '/2/');

        $list['param decimal 22.5 matches 22\.5'] = $this->makeAssertionItem('param_decimal', '/22\.5/');
        $list['param decimal 22.5 matches 22\.']  = $this->makeAssertionItem('param_decimal', '/22\./');
        $list['param decimal 22.5 matches 22']    = $this->makeAssertionItem('param_decimal', '/22/');
        $list['param decimal 22.5 matches \.5']   = $this->makeAssertionItem('param_decimal', '/\.5/');
        $list['param decimal 22.5 matches 5']     = $this->makeAssertionItem('param_decimal', '/5/');
        
        $list['param decimal 11.5 matches 11.5'] = $this->makeAssertionItem('param_decimal_string', '/11\.5/');
        $list['param decimal 11.5 matches 11\.'] = $this->makeAssertionItem('param_decimal_string', '/11\./');
        $list['param decimal 11.5 matches 11']   = $this->makeAssertionItem('param_decimal_string', '/11/');
        $list['param decimal 11.5 matches \.5']  = $this->makeAssertionItem('param_decimal_string', '/\.5/');
        $list['param decimal 11.5 matches 5']    = $this->makeAssertionItem('param_decimal_string', '/5/');

        $list['param string Coração!# matches Cor']        = $this->makeAssertionItem('param_string', '/Cor/');
        $list['param string Coração!# matches raç']        = $this->makeAssertionItem('param_string', '/raç/');
        $list['param string Coração!# matches ção!#']      = $this->makeAssertionItem('param_string', '/ção!#/');
        $list['param string Coração!# matches [a-z]*']     = $this->makeAssertionItem('param_string', '/[a-z]*/');
        $list['param string Coração!# matches [a-zçã!#]*'] = $this->makeAssertionItem('param_string', '/[a-zçã!#]*/');

        $list['param boolean true matches [0-9]{1}']  = $this->makeAssertionItem('param_true', '/[0-9]{1}/');
        $list['param boolean true matches 1']         = $this->makeAssertionItem('param_true', '/1/');
        $list['param boolean false matches [0-9]{1}'] = $this->makeAssertionItem('param_false', '/[0-9]{1}/');
        $list['param boolean false matches 0']        = $this->makeAssertionItem('param_false', '/0/');

        $list["array matches 11"]         = $this->makeAssertionItem('param_array', "/11/");
        $list["array matches 22"]         = $this->makeAssertionItem('param_array', "/22/");
        $list["array matches \.5"]        = $this->makeAssertionItem('param_array', "/\.5/");
        $list["array matches 1\.5"]       = $this->makeAssertionItem('param_array', "/1\.5/");
        $list["array matches [a-zçã!#]*"] = $this->makeAssertionItem('param_array', "/[a-zçã!#]*/");

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

        $input->assert($paramName)->notMatches($valueOne);

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

        $input->assert($paramName)->notMatches($valueOne);

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

        $input->assert($paramName)->notMatches($valueOne);
        
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

        $input->assert($paramName)->notMatches('xx', 'xx');
        
        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
