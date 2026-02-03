<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class EndsWithTest extends AssertionCase
{
    use HasProviderInvalidValue;
    use HasProviderFieldNotExist;

    /**
     * @param array<string,mixed> $httpParams
     * @return array<string,mixed>
     */
    protected function popHttpArrayParam(array &$httpParams): array
    {
        array_pop($httpParams['param_array']);

        return $httpParams;
    }

    /**
     * Recebe um valor (texto, inteiro ou decimal) transformado em texto
     * Compara com um valor (texto, inteiro ou decimal) transformado em texto
     * @return array<string,array<int,mixed>>
     */
    public function validProvider(): array
    {
        $list = [];

        $httpParams = $this->getHttpParams();

        $list['string 222 ends with string 222']   = $this->makeAssertionItem('param_int_string', '222', $httpParams);
        $list['string 22.5 ends with string 22.5'] = $this->makeAssertionItem('param_decimal', '22.5', $httpParams);
        $list['string Coração!# ends with ção!#']  = $this->makeAssertionItem('param_string', 'ção!#', $httpParams);

        // 'param_array' [
        //     111,    // inteiro
        //     '222',  // inteiro string
        //     22.5,   // decimal
        //     '11.5', // decimal string
        //     'ção!#' // string
        // ]

        // termina com 'ção!#'
        $list["array ends with ção!#"] = $this->makeAssertionItem('param_array', 'ção!#', $httpParams);

        $httpParams = $this->popHttpArrayParam($httpParams); // remove 'ção!#', agora termina com 11.5
        $list["array ends with decimal 11.5"] = $this->makeAssertionItem('param_array', 11.5, $httpParams);
        $list["array ends with decimal string 11.5"] = $this->makeAssertionItem('param_array', '11.5', $httpParams);

        $httpParams = $this->popHttpArrayParam($httpParams); // remove '11.5', agora termina com 22.5
        $list["array ends with decimal 22.5"] = $this->makeAssertionItem('param_array', 22.5, $httpParams);
        $list["array ends with decimal string 22.5"] = $this->makeAssertionItem('param_array', '22.5', $httpParams);

        $httpParams = $this->popHttpArrayParam($httpParams);  // remove 22.5, agora termina com 222
        $list["array ends with integer 222"] = $this->makeAssertionItem('param_array', 222, $httpParams);
        $list["array ends with integer string 222"] = $this->makeAssertionItem('param_array', '222', $httpParams);

        $httpParams = $this->popHttpArrayParam($httpParams); // remove '222', agora termina com 111
        $list["array ends with integer 111"] = $this->makeAssertionItem('param_array', 111, $httpParams);
        $list["array ends with integer string 111"] = $this->makeAssertionItem('param_array', '111', $httpParams);

        return $list;
    }

    /**
     * @return array<string,array<int,mixed>>
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function invalidProvider(): array
    {
        $httpParams = $this->getHttpParams();

        $list = [];

        $list['param int 111 not ends with 111'] = $this->makeAssertionItem('param_int', 112, $httpParams);

        $list['param int string 222 not ends with string 222'] = $this->makeAssertionItem(
            'param_int_string',
            '223',
            $httpParams
        );

        $list['param decimal 22.5 not ends with string 22.'] = $this->makeAssertionItem(
            'param_decimal',
            '22.',
            $httpParams
        );

        $list['param decimal 22.5 not ends with string 22'] = $this->makeAssertionItem(
            'param_decimal',
            '22',
            $httpParams
        );

        $list['param decimal 22.5 not ends with string 2'] = $this->makeAssertionItem(
            'param_decimal',
            '2',
            $httpParams
        );

        $list['param decimal 22.5 not ends with decimal 22.'] = $this->makeAssertionItem(
            'param_decimal',
            22.,
            $httpParams
        );

        $list['param decimal 22.5 not ends with int 22'] = $this->makeAssertionItem(
            'param_decimal',
            22,
            $httpParams
        );

        $list['param decimal 22.5 not ends with int 2'] = $this->makeAssertionItem('param_decimal', 2, $httpParams);

        $list['param decimal 11.5 not ends with string 11.'] = $this->makeAssertionItem(
            'param_decimal_string',
            '11.',
            $httpParams
        );

        $list['param decimal 11.5 not ends with string 11'] = $this->makeAssertionItem(
            'param_decimal_string',
            '11',
            $httpParams
        );

        $list['param decimal 11.5 not ends with string 1'] = $this->makeAssertionItem(
            'param_decimal_string',
            '1',
            $httpParams
        );

        $list['param decimal 11.5 not ends with decimal 11.'] = $this->makeAssertionItem(
            'param_decimal_string',
            11.,
            $httpParams
        );

        $list['param decimal 11.5 not ends with int 11'] = $this->makeAssertionItem(
            'param_decimal_string',
            11,
            $httpParams
        );

        $list['param decimal 11.5 not ends with int 1'] = $this->makeAssertionItem(
            'param_decimal_string',
            1,
            $httpParams
        );

        $list['param string Coração!# not ends with Cora'] = $this->makeAssertionItem(
            'param_string',
            'Cora',
            $httpParams
        );

        $list['param string Coração!# not ends with ção!'] = $this->makeAssertionItem(
            'param_string',
            'ção!',
            $httpParams
        );

        $list['param boolean false not ends with 1'] = $this->makeAssertionItem(
            'param_false',
            '1',
            $httpParams
        );

        $list['param boolean false not ends with int 1'] = $this->makeAssertionItem(
            'param_false',
            1,
            $httpParams
        );

        $list['param boolean true not ends with 0'] = $this->makeAssertionItem(
            'param_true',
            '2',
            $httpParams
        );

        $list['param boolean true not ends with int 0'] = $this->makeAssertionItem(
            'param_true',
            2,
            $httpParams
        );

        // array [
        //    111,    // inteiro
        //    '222',  // inteiro string
        //    22.5,   // decimal
        //    '11.5', // decimal string
        //    'ção!#' // string
        // ]

        // O array termina com 'ção!#'. Todos os outros elementos não valem
        $list["array not ends with integer 112"] = $this->makeAssertionItem(
            'param_array',
            112,
            $httpParams
        );

        $list["array not ends with integer 112"] = $this->makeAssertionItem(
            'param_array',
            '112',
            $httpParams
        );

        $list["array not ends with integer string 222"] = $this->makeAssertionItem(
            'param_array',
            221,
            $httpParams
        );

        $list["array not ends with integer string 222"] = $this->makeAssertionItem(
            'param_array',
            '221',
            $httpParams
        );

        $list["array not ends with decimal 22.5"] = $this->makeAssertionItem(
            'param_array',
            22.4,
            $httpParams
        );

        $list["array not ends with decimal 22.5"] = $this->makeAssertionItem(
            'param_array',
            '22.4',
            $httpParams
        );

        $list["array not ends with decimal string 11.5"] = $this->makeAssertionItem(
            'param_array',
            11.4,
            $httpParams
        );

        $list["array not ends with decimal string 11.5"] = $this->makeAssertionItem(
            'param_array',
            '11.4',
            $httpParams
        );

        // O array termina com 'ção!#', não com parte dele
        $list["array not ends with string ção!"] = $this->makeAssertionItem(
            'param_array',
            'ção!',
            $httpParams
        );

        $httpParams = $this->popHttpArrayParam($httpParams); // remove 'ção!#', agora termina com 11.5
        $list["array not ends with decimal string 11.4"] = $this->makeAssertionItem(
            'param_array',
            '11.4',
            $httpParams
        );

        $httpParams = $this->popHttpArrayParam($httpParams); // remove 11.5, agora termina com 22.5
        $list["array not ends with decimal 22.4"] = $this->makeAssertionItem(
            'param_array',
            22.4,
            $httpParams
        );

        $httpParams = $this->popHttpArrayParam($httpParams); // remove 22.5, agora termina com 222
        $list["array not ends with integer string 221"] = $this->makeAssertionItem(
            'param_array',
            '221',
            $httpParams
        );

        $httpParams = $this->popHttpArrayParam($httpParams); // remove 222, agora termina com 111
        $list["array not ends with integer 110"] = $this->makeAssertionItem(
            'param_array',
            110,
            $httpParams
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

        $input->assert($paramName)->endsWith($valueOne);

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

        $input->assert($paramName)->endsWith($valueOne);

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

        $input->assert($paramName)->endsWith($valueOne);

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

        $input->assert($paramName)->endsWith('xx');

        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
