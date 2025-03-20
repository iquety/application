<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

class EqualToTest extends AssertionCase
{
    use AssertionHasDefaultParams;
    use AssertionHasObjectValue;
    use AssertionHasFieldExists;

    public function setUpProvider(): void
    {
        $this->setAssertionMethod('equalTo');

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
        
        $list['param int 111 equal to int 111']           = $this->makeAssertionItem('param_int', 111);
        $list['param int 111 equal to int string 111']    = $this->makeAssertionItem('param_int', '111');
        $list['param int string 222 equal to int 222']    = $this->makeAssertionItem('param_int_string', 222);
        $list['param int string 222 equal to string 222'] = $this->makeAssertionItem('param_int_string', '222');

        $list['param decimal 22.5 equal to decimal 22.5']               = $this->makeAssertionItem('param_decimal', 22.5);
        $list['param decimal 22.5 equal to decimal string 22.5']        = $this->makeAssertionItem('param_decimal', '22.5');
        $list['param decimal string 11.5 equal to decimal 11.5']        = $this->makeAssertionItem('param_decimal_string', 11.5);
        $list['param decimal string 11.5 equal to decimal string 11.5'] = $this->makeAssertionItem('param_decimal_string', '11.5');
        
        $list['param string Coração!# equal to Coração!#'] = $this->makeAssertionItem('param_string', 'Coração!#');
        
        $list['param boolean false equal to 0']     = $this->makeAssertionItem('param_false', '0');
        $list['param boolean false equal to int 0'] = $this->makeAssertionItem('param_false', 0);
        $list['param boolean true equal to 1']      = $this->makeAssertionItem('param_true', '1');
        $list['param boolean true equal to int 1']  = $this->makeAssertionItem('param_true', 1);

        $list["array equal to array"] = $this->makeAssertionItem('param_array', $this->httpParamList['param_array']);

        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $this->setUpProvider();

        $list = [];

        $list['param int 111 not equal to int 112']                  = $this->makeAssertionItem('param_int', 112);
        $list['param int string 222 not equal to int 221']           = $this->makeAssertionItem('param_int_string', 221);
        $list['param decimal 22.5 not equal to decimal 22.4']        = $this->makeAssertionItem('param_decimal', 22.4);
        $list['param decimal string 11.5 not equal to decimal 11.4'] = $this->makeAssertionItem('param_decimal_string', 11.4);
        $list['param string Coração!# not equal to oração!#']        = $this->makeAssertionItem('param_string', 'oração!#');
        
        $list['param boolean false not equal to 1']     = $this->makeAssertionItem('param_false', '1');
        $list['param boolean false not equal to int 1'] = $this->makeAssertionItem('param_false', 1);
        $list['param boolean true not equal to 0']      = $this->makeAssertionItem('param_true', '0');
        $list['param boolean true not equal to 1']      = $this->makeAssertionItem('param_true', 0);

        /** @var array<string,mixed> */
        $array = $this->httpParamList['param_array'];

        array_shift($array);

        $list["array not equal to array"] = $this->makeAssertionItem('param_array', $array);

        return $list;
    }
}
