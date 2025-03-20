<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

class MaxLengthTest extends AssertionCase
{
    use AssertionHasDefaultParams;
    use AssertionHasNumericValue;
    use AssertionHasObjectValue;
    use AssertionHasFieldExists;

    public function setUpProvider(): void
    {
        $this->setAssertionMethod('maxLength');

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

        $list['param int 111 has a maximum of int 111']    = $this->makeAssertionItem('param_int', 111);
        $list['param int 111 has a maximum of string 111'] = $this->makeAssertionItem('param_int', '111');

        $list['param int 111 has a maximum of int 112']    = $this->makeAssertionItem('param_int', 112);
        $list['param int 111 has a maximum of string 112'] = $this->makeAssertionItem('param_int', '112');

        $list['param int string 222 has a maximum of int 222']    = $this->makeAssertionItem('param_int_string', 222);
        $list['param int string 222 has a maximum of string 222'] = $this->makeAssertionItem('param_int_string', '222');

        $list['param int string 222 has a maximum of int 223']    = $this->makeAssertionItem('param_int_string', 223);
        $list['param int string 222 has a maximum of string 223'] = $this->makeAssertionItem('param_int_string', '223');

        $list['param decimal 22.5 has a maximum of decimal 22.5'] = $this->makeAssertionItem('param_decimal', 22.5);
        $list['param decimal 22.5 has a maximum of string 22.5']  = $this->makeAssertionItem('param_decimal', '22.5');

        $list['param decimal 22.5 has a maximum of decimal 22.6'] = $this->makeAssertionItem('param_decimal', 22.6);
        $list['param decimal 22.5 has a maximum of string 22.6']  = $this->makeAssertionItem('param_decimal', '22.6');

        $list['param string Coração!# has a maximum of 9']  = $this->makeAssertionItem('param_string', 9);
        $list['param string Coração!# has a maximum of 10'] = $this->makeAssertionItem('param_string', 10);

        $list['param boolean true has a maximum 1']     = $this->makeAssertionItem('param_true', '1');
        $list['param boolean true has a maximum int 1'] = $this->makeAssertionItem('param_true', 1);

        $list['param boolean true has a maximum 2']     = $this->makeAssertionItem('param_true', '2');
        $list['param boolean true has a maximum int 2'] = $this->makeAssertionItem('param_true', 2);

        $list['param boolean false has a maximum 0']     = $this->makeAssertionItem('param_false', '0');
        $list['param boolean false has a maximum int 0'] = $this->makeAssertionItem('param_false', 0);

        $list['param boolean false has a maximum 1']     = $this->makeAssertionItem('param_false', '1');
        $list['param boolean false has a maximum int 1'] = $this->makeAssertionItem('param_false', 1);

        $list["array has a maximum of 5"] = $this->makeAssertionItem('param_array', 5);
        $list["array has a maximum of 6"] = $this->makeAssertionItem('param_array', 6);

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $this->setUpProvider();

        $list = [];

        $list['param int 111 not has a maximum of int 110']    = $this->makeAssertionItem('param_int', 110);
        $list['param int 111 not has a maximum of string 110'] = $this->makeAssertionItem('param_int', '110');

        $list['param int string 222 not has a maximum of int 221']    = $this->makeAssertionItem('param_int_string', 221);
        $list['param int string 222 not has a maximum of string 221'] = $this->makeAssertionItem('param_int_string', '221');

        $list['param int string 222 not has a maximum of int 221']    = $this->makeAssertionItem('param_int_string', 221);
        $list['param int string 222 not has a maximum of string 221'] = $this->makeAssertionItem('param_int_string', '221');

        $list['param decimal 22.5 not has a maximum of decimal 22.4']        = $this->makeAssertionItem('param_decimal', 22.4);
        $list['param decimal 22.5 not has a maximum of decimal string 22.4'] = $this->makeAssertionItem('param_decimal', '22.4');

        $list['param decimal string 11.5 not has a maximum of decimal 11.4'] = $this->makeAssertionItem('param_decimal_string', 11.4);
        $list['param decimal string 11.5 not has a maximum of string 11.4']  = $this->makeAssertionItem('param_decimal_string', '11.4');

        $list['param string Coração!# not has a maximum of 8'] = $this->makeAssertionItem('param_string', 8);
        $list['param string Coração!# not has a maximum of 8'] = $this->makeAssertionItem('param_string', '8');

        $list['param boolean true not has a maximum 1']     = $this->makeAssertionItem('param_true', '0');
        $list['param boolean true not has a maximum int 1'] = $this->makeAssertionItem('param_true', 0);

        $list["array not has a maximum of 4"] = $this->makeAssertionItem('param_array', 4);
        $list["array not has a maximum of 4"] = $this->makeAssertionItem('param_array', '4');

        return $list;
    }
}
