<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;

class IsDateTimeTest extends AssertionCase
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
            'iso_8601'               => '2024-12-31 23:59:59',
            'european_format'        => '31/12/2024 23:59:59',
            'us_format_am'           => '12/31/2024 11:59:59 AM',
            'us_format_pm'           => '12/31/2024 11:59:59 PM',
            'alternative_format'     => '2024.12.31 23:59:59',
            'abbreviated_month_name' => '31-Dec-2024 23:59:59',
            'full_month_name'        => 'December 31, 2024 23:59:59',
        ];

        $list = [];
        
        foreach(array_keys($httpParams) as $param) {
            $label = $this->paramToLabel($param);

            $list[$label] = $this->makeAssertionItem($param, $httpParams);
        }

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $httpParams = [
            'iso_8601_dirty'                => '00002024-12-31xxx 23:59:59',
            'european_format_dirty'         => '31/12//2024 23:59:59',
            'us_format_dirty'               => 'xxx12/31/2024 11:59:59 PM',
            'alternative_format_dirty'      => 'rr2x024.12.31 23:59:59',
            'abbreviated_month_name_dirty'  => 'xxx31-Dec-2024 23:59:59',
            'full_month_name_dirty'         => 'xxxDecember 31, 2024 23:59:59',
            'iso_8601_invalid_month'        => '2024-13-31 23:59:59',
            'iso_8601_invalid_day'          => '2024-12-32 23:59:59',
            'iso_8601_invalid_hour'         => '2024-12-31 25:59:59',
            'iso_8601_invalid_minute'       => '2024-12-31 20:62:59',
            'iso_8601_invalid_second'       => '2024-12-31 20:59:62',
            'european_format_month'         => '31/13/2024 23:59:59',
            'european_format_day'           => '32/12/2024 23:59:59',
            'european_format_hour'          => '31/12/2024 28:59:59',
            'european_format_minute'        => '31/12/2024 23:62:59',
            'european_format_second'        => '31/12/2024 23:59:62',
            'us_format_month_am'            => '13/31/2024 11:59:59 AM',
            'us_format_day_am'              => '12/32/2024 11:59:59 AM',
            'us_format_hour_am'             => '12/31/2024 26:59:59 AM',
            'us_format_minute_am'           => '12/31/2024 11:62:59 AM',
            'us_format_second_am'           => '12/31/2024 11:59:62 AM',
            'us_format_month_pm'            => '13/31/2024 11:59:59 PM',
            'us_format_day_pm'              => '12/32/2024 11:59:59 PM',
            'us_format_hour_pm'             => '12/31/2024 26:59:59 PM',
            'us_format_minute_pm'           => '12/31/2024 11:62:59 PM',
            'us_format_second_pm'           => '12/31/2024 11:59:62 PM',
            'alternative_format_month'      => '2024.13.31 23:59:59',
            'alternative_format_day'        => '2024.12.32 23:59:59',
            'alternative_format_hour'       => '2024.12.31 27:59:59',
            'alternative_format_minute'     => '2024.12.31 23:62:59',
            'alternative_format_second'     => '2024.12.31 23:59:62',
            'abbreviated_month_name_month'  => '31-Err-2024 23:59:59',
            'abbreviated_month_name_day'    => '32-Dec-2024 23:59:59',
            'abbreviated_month_name_hour'   => '31-Dec-2024 27:59:59',
            'abbreviated_month_name_minute' => '31-Dec-2024 23:62:59',
            'abbreviated_month_name_second' => '31-Dec-2024 23:59:62',
            'full_month_name_month'         => 'Invalid 31, 2024 23:59:59',
            'full_month_name_day'           => 'December 32, 2024 23:59:59',
            'full_month_name_hour'          => 'December 31, 2024 27:59:59',
            'full_month_name_minute'        => 'December 31, 2024 23:62:59',
            'full_month_name_second'        => 'December 31, 2024 23:59:62',
            'empty_string'                  => '',
            'one_space_string'              => ' ',
            'two_spaces_string'             => '  ',
            'array'                         => ['a'],
            'param_false'                   => false,
            'param_true'                    => true,
            'param_string_false'            => 'false',
            'param_string_true'             => 'true',
            'integer'                       => 12345,
        ];

        $list = [];

        foreach(array_keys($httpParams) as $param) {
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

        $input->assert($paramName)->isDateTime();

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

        $input->assert($paramName)->isDateTime();

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

        $input->assert($paramName)->isDateTime();
        
        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
