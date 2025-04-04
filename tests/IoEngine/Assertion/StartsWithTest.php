<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

class StartsWithTest extends AssertionCase
{
    use HasProviderInvalidValue;
    use HasProviderFieldNotExist;

    /**
     * @param array<string,mixed> $httpParams
     * @return array<string,mixed>
     */
    protected function shiftHttpArrayParam(array &$httpParams): array
    {
        array_shift($httpParams['param_array']);

        return $httpParams;
    }

    /**
     * Recebe um valor (texto, inteiro ou decimal) transformado em texto 
     * Compara com um valor (texto, inteiro ou decimal) transformado em texto
     * @return array<string,array<int,mixed>>
     */
    public function validProvider(): array
    {
        $httpParams = $this->getHttpParams();

        $list = [];

        $list['param int 111 starts with string 11'] = $this->makeAssertionItem(
            'param_int',
            '11',
            $httpParams
        );

        $list['param int string 222 starts with string 22'] = $this->makeAssertionItem(
            'param_int_string',
            '22',
            $httpParams
        );

        $list['param decimal 22.5 starts with string 22.']  = $this->makeAssertionItem(
            'param_decimal',
            '22.',
            $httpParams
        );

        $list['param string Coração!# starts with Cora'] = $this->makeAssertionItem(
            'param_string',
            'Cora',
            $httpParams
        );

        // array [
        //    111,    // inteiro
        //    '222',  // inteiro string
        //    22.5,   // decimal
        //    '11.5', // decimal string
        //    'ção!#' // string
        // ]

        // inicia com 111
        $list["array starts with integer string 111"] = $this->makeAssertionItem('param_array', '111', $httpParams);
        $list["array starts with integer 111"]        = $this->makeAssertionItem('param_array', 111, $httpParams);

        $httpParams = $this->shiftHttpArrayParam($httpParams); // remove 111, agora inicia com '222'
        $list["array starts with integer string 222"] = $this->makeAssertionItem('param_array', '222', $httpParams);
        $list["array starts with integer 222"]        = $this->makeAssertionItem('param_array', 222, $httpParams);

        $httpParams = $this->shiftHttpArrayParam($httpParams); // remove '222', agora inicia com 22.5
        $list["array starts with decimal string 22.5"] = $this->makeAssertionItem('param_array', '22.5', $httpParams);
        $list["array starts with decimal 22.5"]        = $this->makeAssertionItem('param_array', 22.5, $httpParams);

        $httpParams = $this->shiftHttpArrayParam($httpParams); // remove 22.5, agora inicia com '11.5'
        $list["array starts with decimal string 11.5"] = $this->makeAssertionItem('param_array', '11.5', $httpParams);
        $list["array starts with decimal 11.5"]        = $this->makeAssertionItem('param_array', 11.5, $httpParams);

        $httpParams = $this->shiftHttpArrayParam($httpParams); // remove '11.5', agora inicia com 'ção!#'
        $list["array starts with ção!#"] = $this->makeAssertionItem('param_array', 'ção!#', $httpParams);
        
        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $httpParams = $this->getHttpParams();
        
        $list = [];
        
        $list['param int 111 not starts with 112']                       = ['param_int', 112, $httpParams];
        $list['param int string 222 not starts with string 223']         = ['param_int_string', '223', $httpParams];
        $list['param int string 222 not starts with integer string 223'] = ['param_int_string',223,$httpParams];
        $list['param decimal 22.5 not starts with string 21.']           = ['param_decimal','21.',$httpParams];
        $list['param decimal 22.5 not starts with string 21']            = ['param_decimal', '21', $httpParams];
        $list['param decimal 22.5 not starts with string 1']             = ['param_decimal', '1', $httpParams];
        $list['param decimal 22.5 not starts with decimal 21.']          = ['param_decimal', 21., $httpParams];
        $list['param decimal 22.5 not starts with int 21']               = ['param_decimal', 21, $httpParams];
        $list['param decimal 22.5 not starts with int 1']                = ['param_decimal', 1, $httpParams];
        $list['param decimal 11.5 not starts with string 12.']  = ['param_decimal_string', '12.', $httpParams];
        $list['param decimal 11.5 not starts with string 12']   = ['param_decimal_string', '12', $httpParams];
        $list['param decimal 11.5 not starts with string 2']    = ['param_decimal_string', '2', $httpParams];
        $list['param decimal 11.5 not starts with decimal 12.'] = ['param_decimal_string', 12., $httpParams];
        $list['param decimal 11.5 not starts with int 12']      = ['param_decimal_string', 12, $httpParams];
        $list['param decimal 11.5 not starts with int 2']       = ['param_decimal_string', 2, $httpParams];
        $list['param string Coração!# not starts with raç']     = ['param_string', 'raç', $httpParams];
        $list['param string Coração!# not starts with ção!']    = ['param_string', 'ção!', $httpParams];
        $list['param boolean false not starts with 1']          = ['param_false', '1', $httpParams];
        $list['param boolean false not starts with int 1']      = ['param_false', 1, $httpParams];
        $list['param boolean true not starts with 0']           = ['param_true', '2', $httpParams];
        $list['param boolean true not starts with int 0']       = ['param_true', 2, $httpParams];

        // array [
        //    111,    // inteiro
        //    '222',  // inteiro string
        //    22.5,   // decimal
        //    '11.5', // decimal string
        //    'ção!#' // string
        // ]

        // O array inicia com '111'. Todos os outros elementos não valem
        $list["array not starts with integer 112"]         = ['param_array', 112, $httpParams];
        $list["array not starts with integer string 112"]  = ['param_array', '112', $httpParams];
        $list["array not starts with integer 221"]         = ['param_array', 221, $httpParams];
        $list["array not starts with integer string 221"]  = ['param_array', '221', $httpParams];
        $list["array not starts with decimal 22.4"]        = ['param_array', 22.4, $httpParams];
        $list["array not starts with decimal string 22.4"] = ['param_array', '22.4', $httpParams];
        $list["array not starts with decimal 11.4"]        = ['param_array', 11.4, $httpParams];
        $list["array not starts with decimal string 11.4"] = ['param_array', '11.4', $httpParams];

        // O array inicia com 111, não com parte dele
        $list["array not starts with integer 11"]        = ['param_array', 11, $httpParams];
        $list["array not starts with integer string 11"] = ['param_array', '11', $httpParams];
        $list["array not starts with integer 1"]         = ['param_array', 1, $httpParams];
        $list["array not starts with integer string 1"]  = ['param_array', '1', $httpParams];

        $httpParams = $this->shiftHttpArrayParam($httpParams); // remove '111', agora inicia com '222'
        $list["array not starts with integer string 221"] = ['param_array', '221', $httpParams];
        $list["array not starts with integer 221"] = ['param_array', 221, $httpParams];

        $httpParams = $this->shiftHttpArrayParam($httpParams); // remove '222', agora inicia com 22.5
        $list["array not starts with decimal string 22.4"] = ['param_array', '22.4', $httpParams];
        $list["array not starts with decimal 22.4"] = ['param_array', 22.4, $httpParams];

        $httpParams = $this->shiftHttpArrayParam($httpParams); // remove 22.5, agora inicia com '11.5'
        $list["array not starts with decimal string 11.4"] = ['param_array', '11.4', $httpParams];
        $list["array not starts with decimal 11.4"] = ['param_array', 11.4, $httpParams];

        $httpParams = $this->shiftHttpArrayParam($httpParams); // remove '11.5', agora inicia com 'ção!#'
        $list["array not starts with ão!#"] = ['param_array', 'ão!#', $httpParams];

        return array_map(
            fn($values) => $this->makeAssertionItem(...$values),
            $list
        );
        
        return $list;
    }

    /**
     * @test
     * @param array<string,mixed> $httpParams
     * @dataProvider validProvider
     */
    public function valueAsserted(string $paramName, mixed $valueOne, array $httpParams): void
    {
        $input = Input::fromString(
            '/user/edit/03?' . http_build_query($httpParams),
        );

        $input->assert($paramName)->startsWith($valueOne);

        // se a asserção não passar, uma exceção será lançada
        $input->validOrResponse();

        // se chegar até aqui... tudo correu bem
        $this->assertTrue(true);
    }

    /**
     * Recebe um valor (texto, inteiro ou decimal) transformado em texto 
     * Compara com um valor (texto, inteiro ou decimal) transformado em texto
     * @test
     * @param array<string,mixed> $httpParams
     * @dataProvider invalidProvider
     */
    public function valueNotAsserted(string $paramName, mixed $valueOne, array $httpParams): void
    {
        $this->expectException(AssertionResponseException::class);
        $this->expectExceptionMessage('The value was not successfully asserted');

        $input = Input::fromString(
            '/user/edit/03?' . http_build_query($httpParams),
        );

        $input->assert($paramName)->startsWith($valueOne);

        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }

    /**
     * @test
     * @dataProvider invalidObjectArgumentsProvider
     */
    public function valueIsInvalidObject(string $paramName, mixed $valueOne, mixed $valueTwo): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument is not valid');

        $input = Input::fromString('/user/edit/03?' . http_build_query([
            'param_string' => 'text',
            'param_int'    => 123,
            'param_float'  => 12.3,
            'param_array'  => ['one', 'two'],
        ]));

        $input->assert($paramName)->startsWith($valueOne, $valueTwo);
        
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

        $input->assert($paramName)->startsWith('xx');
        
        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
