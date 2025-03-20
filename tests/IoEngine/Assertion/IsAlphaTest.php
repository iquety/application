<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

class IsAlphaTest extends AssertionCase
{
    use AssertionHasFieldExists;

    public function setUpProvider(): void
    {
        $this->setAssertionMethod('isAlpha');
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
            'param_text_1'  => 'TEXTO',
            'param_text_2'  => 'abc',
            'param_text_3'  => 'xyz',
            'param_text_4'  => 'TextoABC',
            'param_text_5'  => 'XYZTexto',
            'param_text_6'  => 'TextoXYZ',
            'param_text_7'  => 'TextoABC',
            'param_text_8'  => 'abcxyz',
            'param_text_9'  => 'AbCxYz',
            'param_text_10' => 'texto',
        ]);

        $list = [];
        
        $list['param_text_1']         = $this->makeAssertionItem('param_text_1');
        $list['param_text_2']         = $this->makeAssertionItem('param_text_2');
        $list['param_text_3']         = $this->makeAssertionItem('param_text_3');
        $list['param_text_4']         = $this->makeAssertionItem('param_text_4');
        $list['param_text_5']         = $this->makeAssertionItem('param_text_5');
        $list['param_text_6']         = $this->makeAssertionItem('param_text_6');
        $list['param_text_7']         = $this->makeAssertionItem('param_text_7');
        $list['param_text_8']         = $this->makeAssertionItem('param_text_8');
        $list['param_text_9']         = $this->makeAssertionItem('param_text_9');
        $list['param_text_10']        = $this->makeAssertionItem('param_text_10');

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
            'param_two_spaces_string'              => '  ',
            'param_integer'                        => 123456,
            'param_decimal'                        => 123.456,
            'param_array'                          => ['a'],
            'param_false'                          => false,
            'param_true'                           => true,
        ]);

        $list = [];
        
        $list['param_iso_8601_dirty']                 = $this->makeAssertionItem('param_iso_8601_dirty');
        $list['param_european_format_dirty']          = $this->makeAssertionItem('param_european_format_dirty');
        $list['param_us_format_dirty']                = $this->makeAssertionItem('param_us_format_dirty');
        $list['param_alternative_format_dirty']       = $this->makeAssertionItem('param_alternative_format_dirty');
        $list['param_abbreviated_month_name_dirty']   = $this->makeAssertionItem('param_abbreviated_month_name_dirty');
        $list['param_full_month_name_dirty']          = $this->makeAssertionItem('param_full_month_name_dirty');
        $list['param_iso_8601_invalid_month']         = $this->makeAssertionItem('param_iso_8601_invalid_month');
        $list['param_iso_8601_invalid_day']           = $this->makeAssertionItem('param_iso_8601_invalid_day');
        $list['param_european_format_month']          = $this->makeAssertionItem('param_european_format_month');
        $list['param_european_format_day']            = $this->makeAssertionItem('param_european_format_day');
        $list['param_us_format_month']                = $this->makeAssertionItem('param_us_format_month');
        $list['param_us_format_day']                  = $this->makeAssertionItem('param_us_format_day');
        $list['param_alternative_format_month']       = $this->makeAssertionItem('param_alternative_format_month');
        $list['param_alternative_format_day']         = $this->makeAssertionItem('param_alternative_format_day');
        $list['param_abbreviated_month_name_month']   = $this->makeAssertionItem('param_abbreviated_month_name_month');
        $list['param_abbreviated_month_name_day']     = $this->makeAssertionItem('param_abbreviated_month_name_day');
        $list['param_full_month_name_month']          = $this->makeAssertionItem('param_full_month_name_month');
        $list['param_full_month_name_day']            = $this->makeAssertionItem('param_full_month_name_day');
        $list['param_special_characters']             = $this->makeAssertionItem('param_special_characters');
        $list['param_numbers_and_special_characters'] = $this->makeAssertionItem('param_numbers_and_special_characters');
        $list['param_empty_string']                   = $this->makeAssertionItem('param_empty_string');
        $list['param_one_space_string']               = $this->makeAssertionItem('param_one_space_string');
        $list['param_two_spaces_string']              = $this->makeAssertionItem('param_two_spaces_string');
        $list['param_integer']                        = $this->makeAssertionItem('param_integer');
        $list['param_decimal']                        = $this->makeAssertionItem('param_decimal');
        $list['param_array']                          = $this->makeAssertionItem('param_array');
        $list['param_false']                          = $this->makeAssertionItem('param_false');
        $list['param_true']                           = $this->makeAssertionItem('param_true');

        return $list;
    }
}
