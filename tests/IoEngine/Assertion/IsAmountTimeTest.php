<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

class IsAmountTimeTest extends AssertionCase
{
    use AssertionHasFieldExists;
    
    public function setUpProvider(): void
    {
        $this->setAssertionMethod('isAmountTime');
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
            'param_less_time'      => '00:01:01',
            'param_in_time'        => '23:59:59',
            'param_greater_time'   => '66:59:59',
            'param_greater_time_2' => '999:59:59',
            'param_greater_time_3' => '9999:59:59'
        ]);

        $list = [];
        
        $list['value 00:01:01 is valid'] = $this->makeAssertionItem('param_less_time');
        $list['value 23:59:59 is valid'] = $this->makeAssertionItem('param_in_time');
        $list['value 66:59:59 is valid'] = $this->makeAssertionItem('param_greater_time');
        $list['value 999:59:59 is valid'] = $this->makeAssertionItem('param_greater_time_2');
        $list['value 9999:59:59 is valid'] = $this->makeAssertionItem('param_greater_time_3');
        
        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $this->setUpProvider();

        $this->setAssertionHttpParams([
            'param_invalid_format'    => '00:01,01',
            'param_invalid_seconds'   => '23:59:62',
            'param_invalid_minutes'   => '23:62:59',
            'param_empty_string'      => '',
            'param_one_space_string'  => ' ',
            'param_two_spaces_string' => '  ',
            'param_array'             => ['a'],
            'param_false'             => false,
            'param_true'              => true,
            'param_number'            => 123,
        ]);

        $list = [];
        
        $list['value 00:01,01 is invalid']              = $this->makeAssertionItem('param_invalid_format');
        $list['value 23:59:62 contain invalid seconds'] = $this->makeAssertionItem('param_invalid_seconds');
        $list['value 23:62:59 contain invalid minutes'] = $this->makeAssertionItem('param_invalid_minutes');
        $list['value is a empty string']                = $this->makeAssertionItem('param_empty_string');
        $list['value is a string with one space']       = $this->makeAssertionItem('param_one_space_string');
        $list['value is a string with two spaces']      = $this->makeAssertionItem('param_two_spaces_string');
        $list['value is a array']                       = $this->makeAssertionItem('param_array');
        $list['value is a boolean false']               = $this->makeAssertionItem('param_false');
        $list['value is a boolean true']                = $this->makeAssertionItem('param_true');
        $list['value is number']                        = $this->makeAssertionItem('param_number');

        return $list;
    }
}
