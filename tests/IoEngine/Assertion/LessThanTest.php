<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

class LessThanTest extends AssertionCase
{
    use AssertionHasDefaultParams;
    use AssertionHasNumericValue;
    use AssertionHasObjectValue;
    use AssertionHasFieldExists;

    public function setUpProvider(): void
    {
        $this->setAssertionMethod('lessThan');

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
        
        $list['param int 111 less than int 112']    = $this->makeAssertionItem('param_int', 112);
        $list['param int 111 less than string 112'] = $this->makeAssertionItem('param_int', '112');

        $list['param int string 222 less than int 223']    = $this->makeAssertionItem('param_int_string', 223);
        $list['param int string 222 less than string 223'] = $this->makeAssertionItem('param_int_string', '223');

        $list['param decimal 22.5 less than decimal 22.6'] = $this->makeAssertionItem('param_decimal', 22.6);
        $list['param decimal 22.5 less than string 22.6']  = $this->makeAssertionItem('param_decimal', '22.6');

        $list['param string Coração!# less than 10'] = $this->makeAssertionItem('param_string', 10);

        $list['param boolean true less than 2']     = $this->makeAssertionItem('param_true', '2');
        $list['param boolean true less than int 2'] = $this->makeAssertionItem('param_true', 2);

        $list['param boolean false less than 1'] = $this->makeAssertionItem('param_false', '1');
        $list['param boolean false less than int 1'] = $this->makeAssertionItem('param_false', 1);

        $list["array less than 6"] = $this->makeAssertionItem('param_array', 6);

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $this->setUpProvider();

        $list = [];

        $list['param int 111 not less than int 111']    = $this->makeAssertionItem('param_int', 111);
        $list['param int 111 not less than string 111'] = $this->makeAssertionItem('param_int', '111');

        $list['param int 111 not less than int 110']    = $this->makeAssertionItem('param_int', 110);
        $list['param int 111 not less than string 110'] = $this->makeAssertionItem('param_int', '110');

        $list['param int string 222 not less than int 222']    = $this->makeAssertionItem('param_int_string', 222);
        $list['param int string 222 not less than string 222'] = $this->makeAssertionItem('param_int_string', '222');

        $list['param int string 222 not less than int 220']    = $this->makeAssertionItem('param_int_string', 220);
        $list['param int string 222 not less than string 220'] = $this->makeAssertionItem('param_int_string', '220');

        $list['param decimal 22.5 not less than decimal 22.5']        = $this->makeAssertionItem('param_decimal', 22.5);
        $list['param decimal 22.5 not less than decimal string 22.5'] = $this->makeAssertionItem('param_decimal', '22.5');

        $list['param decimal 22.5 not less than decimal 22.4']        = $this->makeAssertionItem('param_decimal', 22.4);
        $list['param decimal 22.5 not less than decimal string 22.4'] = $this->makeAssertionItem('param_decimal', '22.4');

        $list['param decimal string 11.5 not less than decimal 11.5'] = $this->makeAssertionItem('param_decimal_string', 11.5);
        $list['param decimal string 11.5 not less than string 11.5']  = $this->makeAssertionItem('param_decimal_string', '11.5');

        $list['param string Coração!# not less than 9'] = $this->makeAssertionItem('param_string', 9);
        $list['param string Coração!# not less than 9'] = $this->makeAssertionItem('param_string', '9');

        $list['param string Coração!# not less than 8'] = $this->makeAssertionItem('param_string', 8);
        $list['param string Coração!# not less than 8'] = $this->makeAssertionItem('param_string', '8');

        $list['param boolean true less than 0']     = $this->makeAssertionItem('param_true', '0');
        $list['param boolean true less than int 0'] = $this->makeAssertionItem('param_true', 0);
        $list['param boolean true less than 1']     = $this->makeAssertionItem('param_true', '1');
        $list['param boolean true less than int 1'] = $this->makeAssertionItem('param_true', 1);

        $list['param boolean false less than 0'] = $this->makeAssertionItem('param_false', '0');
        $list['param boolean false less than int 0'] = $this->makeAssertionItem('param_false', 0);
        

        $list["array not less than 5"] = $this->makeAssertionItem('param_array', 5);
        $list["array not less than 5"] = $this->makeAssertionItem('param_array', '5');

        $list["array not less than 4"] = $this->makeAssertionItem('param_array', 4);
        $list["array not less than 4"] = $this->makeAssertionItem('param_array', '4');

        return $list;
    }
}
