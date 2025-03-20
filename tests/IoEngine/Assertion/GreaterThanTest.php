<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

class GreaterThanTest extends AssertionCase
{
    use AssertionHasDefaultParams;
    use AssertionHasNumericValue;
    use AssertionHasObjectValue;
    use AssertionHasFieldExists;

    public function setUpProvider(): void
    {
        $this->setAssertionMethod('greaterThan');

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
        
        $list['param int 111 greater than int 110']    = $this->makeAssertionItem('param_int', 110);
        $list['param int 111 greater than string 110'] = $this->makeAssertionItem('param_int', '110');

        $list['param int 111 greater than int 10']    = $this->makeAssertionItem('param_int', 10);
        $list['param int 111 greater than string 10'] = $this->makeAssertionItem('param_int', '10');

        $list['param int 111 greater than int 1']    = $this->makeAssertionItem('param_int', 1);
        $list['param int 111 greater than string 1'] = $this->makeAssertionItem('param_int', '1');

        $list['param int string 222 greater than string 221'] = $this->makeAssertionItem('param_int_string', '221');
        $list['param int string 222 greater than string 21']  = $this->makeAssertionItem('param_int_string', '21');
        $list['param int string 222 greater than string 1']   = $this->makeAssertionItem('param_int_string', '1');

        $list['param int string 222 greater than int 221'] = $this->makeAssertionItem('param_int_string', 221);
        $list['param int string 222 greater than int 21']  = $this->makeAssertionItem('param_int_string', 21);
        $list['param int string 222 greater than int 1']   = $this->makeAssertionItem('param_int_string', 1);

        $list['param decimal 22.5 greater than string 22.4']  = $this->makeAssertionItem('param_decimal', '22.4');
        $list['param decimal 22.5 greater than decimal 22.4'] = $this->makeAssertionItem('param_decimal', 22.4);

        $list['param string Coração!# greater than 8'] = $this->makeAssertionItem('param_string', 8);
        $list['param string Coração!# greater than 1'] = $this->makeAssertionItem('param_string', 1);
        $list['param string Coração!# greater than 0'] = $this->makeAssertionItem('param_string', 0);

        $list['param boolean true greater than 0'] = $this->makeAssertionItem('param_true', 0);

        $list["array greater than 4"] = $this->makeAssertionItem('param_array', 4);
        $list["array greater than 0"] = $this->makeAssertionItem('param_array', 0);

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $this->setUpProvider();

        $list = [];

        $list['param int 111 not greater than int 111']    = $this->makeAssertionItem('param_int', 111);
        $list['param int 111 not greater than string 111'] = $this->makeAssertionItem('param_int', '111');

        $list['param int 111 not greater than int 112']    = $this->makeAssertionItem('param_int', 112);
        $list['param int 111 not greater than string 112'] = $this->makeAssertionItem('param_int', '112');

        $list['param int string 222 not greater than int 222']    = $this->makeAssertionItem('param_int_string', 222);
        $list['param int string 222 not greater than string 222'] = $this->makeAssertionItem('param_int_string', '222');

        $list['param int string 222 not greater than int 223']    = $this->makeAssertionItem('param_int_string', 223);
        $list['param int string 222 not greater than string 223'] = $this->makeAssertionItem('param_int_string', '223');

        $list['param decimal 22.5 not greater than decimal 22.5']        = $this->makeAssertionItem('param_decimal', 22.5);
        $list['param decimal 22.5 not greater than decimal string 22.5'] = $this->makeAssertionItem('param_decimal', '22.5');

        $list['param decimal 22.5 not greater than decimal 22.6']        = $this->makeAssertionItem('param_decimal', 22.6);
        $list['param decimal 22.5 not greater than decimal string 22.6'] = $this->makeAssertionItem('param_decimal', '22.6');

        $list['param decimal string 11.5 not greater than decimal 11.5'] = $this->makeAssertionItem('param_decimal_string', 11.5);
        $list['param decimal string 11.5 not greater than string 11.5']  = $this->makeAssertionItem('param_decimal_string', '11.5');

        $list['param string Coração!# not greater than 9'] = $this->makeAssertionItem('param_string', 9);
        $list['param string Coração!# not greater than 9'] = $this->makeAssertionItem('param_string', '9');

        $list['param string Coração!# not greater than 10'] = $this->makeAssertionItem('param_string', 10);
        $list['param string Coração!# not greater than 10'] = $this->makeAssertionItem('param_string', '10');
        
        $list['param boolean false greater than 0'] = $this->makeAssertionItem('param_false', '0');
        $list['param boolean false greater than int 0'] = $this->makeAssertionItem('param_false', 0);

        $list['param boolean true greater than 1'] = $this->makeAssertionItem('param_true', '1');
        $list['param boolean true greater than int 1'] = $this->makeAssertionItem('param_true', 1);

        $list["array not greater than 5"] = $this->makeAssertionItem('param_array', 5);
        $list["array not greater than 5"] = $this->makeAssertionItem('param_array', '5');

        $list["array not greater than 6"] = $this->makeAssertionItem('param_array', 6);
        $list["array not greater than 6"] = $this->makeAssertionItem('param_array', '6');

        return $list;
    }
}
