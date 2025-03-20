<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

class MinLengthTest extends AssertionCase
{
    use AssertionHasDefaultParams;
    use AssertionHasNumericValue;
    use AssertionHasObjectValue;
    use AssertionHasFieldExists;

    public function setUpProvider(): void
    {
        $this->setAssertionMethod('minLength');

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

        $list['param int 111 has at least int 111']    = $this->makeAssertionItem('param_int', 111);
        $list['param int 111 has at least string 111'] = $this->makeAssertionItem('param_int', '111');

        $list['param int 111 has at least int 110']    = $this->makeAssertionItem('param_int', 110);
        $list['param int 111 has at least string 110'] = $this->makeAssertionItem('param_int', '110');

        $list['param int string 222 has at least int 222']    = $this->makeAssertionItem('param_int_string', 222);
        $list['param int string 222 has at least string 222'] = $this->makeAssertionItem('param_int_string', '222');

        $list['param int string 222 has at least int 221']    = $this->makeAssertionItem('param_int_string', 221);
        $list['param int string 222 has at least string 221'] = $this->makeAssertionItem('param_int_string', '221');

        $list['param decimal 22.5 has at least decimal 22.5'] = $this->makeAssertionItem('param_decimal', 22.5);
        $list['param decimal 22.5 has at least string 22.5']  = $this->makeAssertionItem('param_decimal', '22.5');

        $list['param decimal 22.5 has at least decimal 22.4'] = $this->makeAssertionItem('param_decimal', 22.4);
        $list['param decimal 22.5 has at least string 22.4']  = $this->makeAssertionItem('param_decimal', '22.4');

        $list['param string Coração!# has at least 9'] = $this->makeAssertionItem('param_string', 9);
        $list['param string Coração!# has at least 8'] = $this->makeAssertionItem('param_string', 8);

        $list['param boolean true has at least 2']     = $this->makeAssertionItem('param_true', '0');
        $list['param boolean true has at least int 2'] = $this->makeAssertionItem('param_true', 0);
        
        $list['param boolean true has at least 1']     = $this->makeAssertionItem('param_true', '1');
        $list['param boolean true has at least int 1'] = $this->makeAssertionItem('param_true', 1);

        $list['param boolean false has at least 0']     = $this->makeAssertionItem('param_false', '0');
        $list['param boolean false has at least int 0'] = $this->makeAssertionItem('param_false', 0);

        $list["array has at least 5"] = $this->makeAssertionItem('param_array', 5);
        $list["array has at least 4"] = $this->makeAssertionItem('param_array', 4);

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $this->setUpProvider();

        $list = [];

        $list['param int 111 not has at least int 112']    = $this->makeAssertionItem('param_int', 112);
        $list['param int 111 not has at least string 112'] = $this->makeAssertionItem('param_int', '112');

        $list['param int string 222 not has at least int 223']    = $this->makeAssertionItem('param_int_string', 223);
        $list['param int string 222 not has at least string 223'] = $this->makeAssertionItem('param_int_string', '223');

        $list['param int string 222 not has at least int 224']    = $this->makeAssertionItem('param_int_string', 224);
        $list['param int string 222 not has at least string 224'] = $this->makeAssertionItem('param_int_string', '224');

        $list['param decimal 22.5 not has at least decimal 22.6']        = $this->makeAssertionItem('param_decimal', 22.6);
        $list['param decimal 22.5 not has at least decimal string 22.6'] = $this->makeAssertionItem('param_decimal', '22.6');

        $list['param decimal string 11.5 not has at least decimal 11.6'] = $this->makeAssertionItem('param_decimal_string', 11.6);
        $list['param decimal string 11.5 not has at least string 11.6']  = $this->makeAssertionItem('param_decimal_string', '11.6');

        $list['param string Coração!# not has at least 10'] = $this->makeAssertionItem('param_string', 10);
        $list['param string Coração!# not has at least 10'] = $this->makeAssertionItem('param_string', '10');

        $list['param boolean true not has at least 2']     = $this->makeAssertionItem('param_true', '2');
        $list['param boolean true not has at least int 2'] = $this->makeAssertionItem('param_true', 2);

        $list['param boolean false not has at least 1']     = $this->makeAssertionItem('param_false', '1');
        $list['param boolean false not has at least int 1'] = $this->makeAssertionItem('param_false', 1);

        $list["array not has at least 6"] = $this->makeAssertionItem('param_array', 6);
        $list["array not has at least 6"] = $this->makeAssertionItem('param_array', '6');

        return $list;
    }
}
