<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

class LessThanOrEqualToTest extends AssertionCase
{
    use AssertionHasDefaultParams;
    use AssertionHasNumericValue;
    use AssertionHasObjectValue;
    use AssertionHasFieldExists;
    
    public function setUpProvider(): void
    {
        $this->setAssertionMethod('lessThanOrEqualTo');

        $this->setAssertionHttpParams($this->getDefaultHttpParams());
    }

    /**
     * Recebe um valor (texto, inteiro ou decimal) transformado em texto 
     * Compara com um valor (texto, inteiro ou decimal) transformado em texto
     * @return array<string,array<int,mixed>>
     */
    public function validProvider(): array
    {
        $this->setUpProvider();

        $list = [];
        
        $list['param int 111 less than or equal to int 111']    = $this->makeAssertionItem('param_int', 111);
        $list['param int 111 less than or equal to string 111'] = $this->makeAssertionItem('param_int', '111');

        $list['param int 111 less than or equal to int 112']    = $this->makeAssertionItem('param_int', 112);
        $list['param int 111 less than or equal to string 112'] = $this->makeAssertionItem('param_int', '112');

        $list['param int string 222 less than or equal to string 222'] = $this->makeAssertionItem('param_int_string', '222');
        $list['param int string 222 less than or equal to int 222']    = $this->makeAssertionItem('param_int_string', 222);
        
        $list['param int string 222 less than or equal to string 223'] = $this->makeAssertionItem('param_int_string', '223');
        $list['param int string 222 less than or equal to int 223']    = $this->makeAssertionItem('param_int_string', 223);
        
        $list['param decimal 22.5 less than or equal to string 22.5']  = $this->makeAssertionItem('param_decimal', '22.5');
        $list['param decimal 22.5 less than or equal to decimal 22.5'] = $this->makeAssertionItem('param_decimal', 22.5);

        $list['param decimal 22.5 less than or equal to string 22.6'] = $this->makeAssertionItem('param_decimal', '22.6');
        $list['param decimal 22.5 less than or equal to decimal 22.6'] = $this->makeAssertionItem('param_decimal', 22.6);

        $list['param decimal 11.5 less than or equal to string 11.5'] = $this->makeAssertionItem('param_decimal_string', '11.5');
        $list['param decimal 11.5 less than or equal to decimal 11.5'] = $this->makeAssertionItem('param_decimal_string', 11.5);

        $list['param decimal 11.5 less than or equal to string 11.6'] = $this->makeAssertionItem('param_decimal_string', '11.6');
        $list['param decimal 11.5 less than or equal to decimal 11.6'] = $this->makeAssertionItem('param_decimal_string', 11.6);

        $list['param string Coração!# less than or equal to 9']  = $this->makeAssertionItem('param_string', 9);
        $list['param string Coração!# less than or equal to 10'] = $this->makeAssertionItem('param_string', 10);

        $list['param boolean true less than or equal 1']     = $this->makeAssertionItem('param_true', '1');
        $list['param boolean true less than or equal int 1'] = $this->makeAssertionItem('param_true', 1);
        $list['param boolean true less than or equal 2']     = $this->makeAssertionItem('param_true', '2');
        $list['param boolean true less than or equal int 2'] = $this->makeAssertionItem('param_true', 2);

        $list['param boolean false less than or equal 0'] = $this->makeAssertionItem('param_false', '0');
        $list['param boolean false less than or equal int 0'] = $this->makeAssertionItem('param_false', 0);
        $list['param boolean false less than or equal 1'] = $this->makeAssertionItem('param_false', '1');
        $list['param boolean false less than or equal int 1'] = $this->makeAssertionItem('param_false', 1);

        $list["array less than or equal to 5"] = $this->makeAssertionItem('param_array', 5);
        $list["array less than or equal to 6"] = $this->makeAssertionItem('param_array', 6);

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $this->setUpProvider();

        $list = [];

        $list['param int 111 not less than or equal to int 110']    = $this->makeAssertionItem('param_int', 110);
        $list['param int 111 not less than or equal to string 110'] = $this->makeAssertionItem('param_int', '110');

        $list['param int string 222 not less than or equal to int 221']    = $this->makeAssertionItem('param_int_string', 221);
        $list['param int string 222 not less than or equal to string 221'] = $this->makeAssertionItem('param_int_string', '221');

        $list['param decimal 22.5 not less than or equal to decimal 22.4']        = $this->makeAssertionItem('param_decimal', 22.4);
        $list['param decimal 22.5 not less than or equal to decimal string 22.4'] = $this->makeAssertionItem('param_decimal', '22.4');

        $list['param decimal string 11.5 not less than or equal to decimal 11.4'] = $this->makeAssertionItem('param_decimal_string', 11.4);
        $list['param decimal string 11.5 not less than or equal to string 11.4']  = $this->makeAssertionItem('param_decimal_string', '11.4');

        $list['param string Coração!# not less than or equal to 8'] = $this->makeAssertionItem('param_string', 8);
        $list['param string Coração!# not less than or equal to 8'] = $this->makeAssertionItem('param_string', '8');

        $list['param boolean true not less than or equal int 0'] = $this->makeAssertionItem('param_true', 0);

        $list["array not less than or equal to 4"] = $this->makeAssertionItem('param_array', 4);
        $list["array not less than or equal to 4"] = $this->makeAssertionItem('param_array', '4');

        return $list;
    }
}
