<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

class IsAlphaNumericTest extends AssertionCase
{
    use AssertionHasFieldExists;

    public function setUpProvider(): void
    {
        $this->setAssertionMethod('isAlphaNumeric');
    }

    /**
     * Recebe um valor (texto, inteiro ou decimal) transformado em texto 
     * Compara com um valor (texto, inteiro ou decimal) transformado em texto
     * @return array<string,array<int,mixed>>
     */
    public function validProvider(): array
    {
        $this->setUpProvider();

        $this->setAssertionHttpParams([
            'param_upper_case_string'         => 'CORAÇÃO',
            'param_lower_case_string'         => 'coração',
            'param_upper_case_string_integer' => 'CORAÇÃO123',
            'param_lower_case string_integer' => 'coração123',
            'param_string_integer'            => '123',
            'param_string_decimal'            => '12.3',
            'param_false'                     => false,          // false é mudado para 0
            'param_true'                      => true,           // true é mudado para 1
            'param_integer'                   => 123,
            'param_decimal'                   => 12.3,
        ]);

        $list = [];
        
        $list['Upper case string']           = $this->makeAssertionItem('param_upper_case_string');
        $list['Lower case string']           = $this->makeAssertionItem('param_lower_case_string');
        $list['Upper case string + integer'] = $this->makeAssertionItem('param_upper_case_string_integer');
        $list['Lower case string + integer'] = $this->makeAssertionItem('param_lower_case_string_integer');
        $list['string integer']              = $this->makeAssertionItem('param_string_integer');
        $list['string decimal']              = $this->makeAssertionItem('param_string_decimal');
        $list['boolean false']                = $this->makeAssertionItem('param_false');
        $list['boolean true']                = $this->makeAssertionItem('param_true');
        $list['integer']                     = $this->makeAssertionItem('param_integer');
        $list['decimal']                     = $this->makeAssertionItem('param_decimal');

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $this->setUpProvider();

        $this->setAssertionHttpParams([
            'param_iso_8601_dirty'                 => '00002024-12-31xxx',
            'param_european_format_dirty'          => '31/12//2024',
            'param_us_format_dirty'                => 'xxx12/31/2024',
            'param_alternative_format_dirty'       => 'rr2x024.12.31',
            'param_abbreviated_month_name_dirty'   => 'xxx31-Dec-2024',
            'param_full_month_name_dirty'          => 'xxxDecember 31, 2024',
            'param_iso_8601_invalid_month'         => '2024-13-31',
            'param_iso_8601_invalid_day'           => '2024-12-32',
            'param_european_format_month'          => '31/13/2024',
            'param_european_format_day'            => '32/12/2024',
            'param_us_format_month'                => '13/31/2024',
            'param_us_format_day'                  => '12/32/2024',
            'param_alternative_format_month'       => '2024.13.31',
            'param_alternative_format_day'         => '2024.12.32',
            'param_abbreviated_month_name_month'   => '31-Err-2024',
            'param_abbreviated_month_name_day'     => '32-Dec-2024',
            'param_full_month_name_month'          => 'Invalid 31, 2024',
            'param_full_month_name_day'            => 'December 32, 2024',
            'param_special_characters'             => '@#$%^&*()',
            'param_numbers_and_special_characters' => '123@#$%',
            'param_empty_string'                   => '',
            'param_one_space_string'               => ' ',
            'param_two_spaces_string'              => ' ',
            'param_array'                          => ['a']
        ]);

        $list = [];
        
        $list['iso_8601_dirty']                 = $this->makeAssertionItem('param_iso_8601_dirty');
        $list['european_format_dirty']          = $this->makeAssertionItem('param_european_format_dirty');
        $list['us_format_dirty']                = $this->makeAssertionItem('param_us_format_dirty');
        $list['alternative_format_dirty']       = $this->makeAssertionItem('param_alternative_format_dirty');
        $list['abbreviated_month_name_dirty']   = $this->makeAssertionItem('param_abbreviated_month_name_dirty');
        $list['full_month_name_dirty']          = $this->makeAssertionItem('param_full_month_name_dirty');
        $list['iso_8601_invalid_month']         = $this->makeAssertionItem('param_iso_8601_invalid_month');
        $list['iso_8601_invalid_day']           = $this->makeAssertionItem('param_iso_8601_invalid_day');
        $list['european_format_month']          = $this->makeAssertionItem('param_european_format_month');
        $list['european_format_day']            = $this->makeAssertionItem('param_european_format_day');
        $list['us_format_month']                = $this->makeAssertionItem('param_us_format_month');
        $list['us_format_day']                  = $this->makeAssertionItem('param_us_format_day');
        $list['alternative_format_month']       = $this->makeAssertionItem('param_alternative_format_month');
        $list['alternative_format_day']         = $this->makeAssertionItem('param_alternative_format_day');
        $list['abbreviated_month_name_month']   = $this->makeAssertionItem('param_abbreviated_month_name_month');
        $list['abbreviated_month_name_day']     = $this->makeAssertionItem('param_abbreviated_month_name_day');
        $list['full_month_name_month']          = $this->makeAssertionItem('param_full_month_name_month');
        $list['full_month_name_day']            = $this->makeAssertionItem('param_full_month_name_day');
        $list['special_characters']             = $this->makeAssertionItem('param_special_characters');
        $list['numbers_and_special_characters'] = $this->makeAssertionItem('param_numbers_and_special_characters');
        $list['empty_string']                   = $this->makeAssertionItem('param_empty_string');
        $list['one_space_string']               = $this->makeAssertionItem('param_one_space_string');
        $list['two_spaces_string']              = $this->makeAssertionItem('param_two_spaces_string');
        $list['array']                          = $this->makeAssertionItem('param_array');

        return $list;
    }
}
