<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

class LengthTest extends AssertionCase
{
    use AssertionHasDefaultParams;
    use AssertionHasNumericValue;
    use AssertionHasObjectValue;
    use AssertionHasFieldExists;
    
    public function setUpProvider(): void
    {
        $this->setAssertionMethod('length');

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

        $list['param int 111 has length int 111']    = $this->makeAssertionItem('param_int', 111);
        $list['param int 111 has length string 111'] = $this->makeAssertionItem('param_int', '111');

        $list['param int string 222 has length int 222']    = $this->makeAssertionItem('param_int_string', 222);
        $list['param int string 222 has length string 222'] = $this->makeAssertionItem('param_int_string', '222');

        $list['param decimal 22.5 has length decimal 22.5'] = $this->makeAssertionItem('param_decimal', 22.5);
        $list['param decimal 22.5 has length string 22.5']  = $this->makeAssertionItem('param_decimal', '22.5');

        $list['param string Coração!# has length 9'] = $this->makeAssertionItem('param_string', 9);

        $list['param boolean true has length 1'] = $this->makeAssertionItem('param_true', '1');
        $list['param boolean true has length int 1'] = $this->makeAssertionItem('param_true', 1);

        $list['param boolean false has length 0'] = $this->makeAssertionItem('param_false', '0');
        $list['param boolean false has length int 0'] = $this->makeAssertionItem('param_false', 0);

        $list["array has length 5"] = $this->makeAssertionItem('param_array', 5);

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $this->setUpProvider();

        $list = [];

        $list['param int 111 not has length int 112']    = $this->makeAssertionItem('param_int', 112);
        $list['param int 111 not has length string 112'] = $this->makeAssertionItem('param_int', '112');

        $list['param int 111 not has length int 110']    = $this->makeAssertionItem('param_int', 110);
        $list['param int 111 not has length string 110'] = $this->makeAssertionItem('param_int', '110');

        $list['param int string 222 not has length int 223']    = $this->makeAssertionItem('param_int_string', 223);
        $list['param int string 222 not has length string 223'] = $this->makeAssertionItem('param_int_string', '223');

        $list['param int string 222 not has length int 221']    = $this->makeAssertionItem('param_int_string', 221);
        $list['param int string 222 not has length string 221'] = $this->makeAssertionItem('param_int_string', '221');

        $list['param decimal 22.5 not has length decimal 22.6']        = $this->makeAssertionItem('param_decimal', 22.6);
        $list['param decimal 22.5 not has length decimal string 22.6'] = $this->makeAssertionItem('param_decimal', '22.6');

        $list['param decimal 22.5 not has length decimal 22.4']        = $this->makeAssertionItem('param_decimal', 22.4);
        $list['param decimal 22.5 not has length decimal string 22.4'] = $this->makeAssertionItem('param_decimal', '22.4');

        $list['param decimal string 11.5 not has length decimal 11.6'] = $this->makeAssertionItem('param_decimal_string', 11.6);
        $list['param decimal string 11.5 not has length string 11.6']  = $this->makeAssertionItem('param_decimal_string', '11.6');

        $list['param decimal string 11.5 not has length decimal 11.4'] = $this->makeAssertionItem('param_decimal_string', 11.4);
        $list['param decimal string 11.5 not has length string 11.4']  = $this->makeAssertionItem('param_decimal_string', '11.4');

        $list['param string Coração!# not has length 10'] = $this->makeAssertionItem('param_string', 10);
        $list['param string Coração!# not has length 10'] = $this->makeAssertionItem('param_string', '10');

        $list['param string Coração!# not has length 8'] = $this->makeAssertionItem('param_string', 8);
        $list['param string Coração!# not has length 8'] = $this->makeAssertionItem('param_string', '8');

        $list['param boolean true not has length 0'] = $this->makeAssertionItem('param_true', '0');
        $list['param boolean true not has length int 0'] = $this->makeAssertionItem('param_true', 0);
        $list['param boolean true not has length 2'] = $this->makeAssertionItem('param_true', '2');
        $list['param boolean true not has length int 2'] = $this->makeAssertionItem('param_true', 2);

        $list['param boolean false not has length 1'] = $this->makeAssertionItem('param_false', '1');
        $list['param boolean false not has length int 1'] = $this->makeAssertionItem('param_false', 1);

        $list["array not has length 6"] = $this->makeAssertionItem('param_array', 6);
        $list["array not has length 6"] = $this->makeAssertionItem('param_array', '6');

        $list["array not has length 4"] = $this->makeAssertionItem('param_array', 4);
        $list["array not has length 4"] = $this->makeAssertionItem('param_array', '4');

        return $list;
    }
}
