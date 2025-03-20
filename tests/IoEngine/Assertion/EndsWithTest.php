<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

class EndsWithTest extends AssertionCase
{
    use AssertionHasDefaultParams;
    use AssertionHasObjectValue;
    use AssertionHasFieldExists;
    
    public function setUpProvider(): void
    {
        $this->setAssertionMethod('endsWith');

        $this->setAssertionHttpParams($this->getDefaultHttpParams());
    }

    protected function popHttpArrayParam(): void
    {
        /** @var array<string,mixed> */
        $array = &$this->httpParamList['param_array'];

        array_pop($array);
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
        
        $list['param int 111 ends with int 111']    = $this->makeAssertionItem('param_int', 111);
        $list['param int 111 ends with string 111'] = $this->makeAssertionItem('param_int', '111');
        $list['param int 111 ends with int 11']     = $this->makeAssertionItem('param_int', 11);
        $list['param int 111 ends with string 11']  = $this->makeAssertionItem('param_int', '11');
        $list['param int 111 ends with int 1']      = $this->makeAssertionItem('param_int', 1);
        $list['param int 111 ends with string 1']   = $this->makeAssertionItem('param_int', '1');

        $list['param int string 222 ends with string 222'] = $this->makeAssertionItem('param_int_string', '222');
        $list['param int string 222 ends with string 22']  = $this->makeAssertionItem('param_int_string', '22');
        $list['param int string 222 ends with string 2']   = $this->makeAssertionItem('param_int_string', '2');
        $list['param int string 222 ends with int 222']    = $this->makeAssertionItem('param_int_string', 222);
        $list['param int string 222 ends with int 22']     = $this->makeAssertionItem('param_int_string', 22);
        $list['param int string 222 ends with int 2']      = $this->makeAssertionItem('param_int_string', 2);

        $list['param decimal 22.5 ends with string 22.5']  = $this->makeAssertionItem('param_decimal', '22.5');
        $list['param decimal 22.5 ends with decimal 22.5'] = $this->makeAssertionItem('param_decimal', 22.5);
        $list['param decimal 22.5 ends with string 2.5']   = $this->makeAssertionItem('param_decimal', '2.5');
        $list['param decimal 22.5 ends with float 2.5']    = $this->makeAssertionItem('param_decimal', 2.5);
        $list['param decimal 22.5 ends with string .5']    = $this->makeAssertionItem('param_decimal', '.5');
        // $list['param decimal 22.5 ends with float .5'] = $this->makeAssertionItem('param_decimal', .5); // não funciona
        $list['param decimal 22.5 ends with string 5']     = $this->makeAssertionItem('param_decimal', '5');
        $list['param decimal 22.5 ends with int 5']        = $this->makeAssertionItem('param_decimal', 5);

        $list['param decimal 11.5 ends with string 11.5']  = $this->makeAssertionItem('param_decimal_string', '11.5');
        $list['param decimal 11.5 ends with decimal 11.5'] = $this->makeAssertionItem('param_decimal_string', 11.5);
        $list['param decimal 11.5 ends with string 1.5']   = $this->makeAssertionItem('param_decimal_string', '1.5');
        $list['param decimal 11.5 ends with float 1.5']    = $this->makeAssertionItem('param_decimal_string', 1.5);
        $list['param decimal 11.5 ends with string .5']    = $this->makeAssertionItem('param_decimal_string', '.5');
        // $list['param decimal 11.5 ends with int .5']  = $this->makeAssertionItem('param_decimal_string', .5); // não funciona
        $list['param decimal 11.5 ends with string 5']     = $this->makeAssertionItem('param_decimal_string', '5');
        $list['param decimal 11.5 ends with int 5']        = $this->makeAssertionItem('param_decimal_string', 5);

        $list['param string Coração!# ends with ção!#'] = $this->makeAssertionItem('param_string', 'ção!#');

        $list['param boolean false ends with 0']     = $this->makeAssertionItem('param_false', '0');
        $list['param boolean false ends with int 0'] = $this->makeAssertionItem('param_false', 0);
        $list['param boolean true ends with 1']      = $this->makeAssertionItem('param_true', '1');
        $list['param boolean true ends with int 1']  = $this->makeAssertionItem('param_true', 1);

        // array [
        //    111,    // inteiro
        //    '222',  // inteiro string
        //    22.5,   // decimal
        //    '11.5', // decimal string
        //    'ção!#' // string
        // ]

        // termina com 'ção!#'
        $list["array ends with ção!#"] = $this->makeAssertionItem('param_array', 'ção!#');

        $this->popHttpArrayParam(); // remove 'ção!#', agora termina com 11.5
        $list["array ends with decimal string 11.5"] = $this->makeAssertionItem('param_array', '11.5');
        $list["array ends with decimal 11.5"]        = $this->makeAssertionItem('param_array', 11.5);

        $this->popHttpArrayParam(); // remove '11.5', agora termina com 22.5
        $list["array ends with decimal string 22.5"] = $this->makeAssertionItem('param_array', '22.5');
        $list["array ends with decimal 22.5"]        = $this->makeAssertionItem('param_array', 22.5);

        $this->popHttpArrayParam(); // remove 22.5, agora termina com 222
        $list["array ends with integer string 222"] = $this->makeAssertionItem('param_array', '222');
        $list["array ends with integer 222"]        = $this->makeAssertionItem('param_array', 222);

        $this->popHttpArrayParam(); // remove '222', agora termina com 111
        $list["array ends with integer string 111"] = $this->makeAssertionItem('param_array', '111');
        $list["array ends with integer 111"]        = $this->makeAssertionItem('param_array', 111);
        
        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $this->setUpProvider();

        $list = [];

        $list['param int 111 not ends with 111']               = $this->makeAssertionItem('param_int', 112);
        $list['param int string 222 not ends with string 222'] = $this->makeAssertionItem('param_int_string', '223');

        $list['param decimal 22.5 not ends with string 22.']  = $this->makeAssertionItem('param_decimal', '22.');
        $list['param decimal 22.5 not ends with string 22']   = $this->makeAssertionItem('param_decimal', '22');
        $list['param decimal 22.5 not ends with string 2']    = $this->makeAssertionItem('param_decimal', '2');
        $list['param decimal 22.5 not ends with decimal 22.'] = $this->makeAssertionItem('param_decimal', 22.);
        $list['param decimal 22.5 not ends with int 22']      = $this->makeAssertionItem('param_decimal', 22);
        $list['param decimal 22.5 not ends with int 2']       = $this->makeAssertionItem('param_decimal', 2);
        
        $list['param decimal 11.5 not ends with string 11.']  = $this->makeAssertionItem('param_decimal_string', '11.');
        $list['param decimal 11.5 not ends with string 11']   = $this->makeAssertionItem('param_decimal_string', '11');
        $list['param decimal 11.5 not ends with string 1']    = $this->makeAssertionItem('param_decimal_string', '1');
        $list['param decimal 11.5 not ends with decimal 11.'] = $this->makeAssertionItem('param_decimal_string', 11.);
        $list['param decimal 11.5 not ends with int 11']      = $this->makeAssertionItem('param_decimal_string', 11);
        $list['param decimal 11.5 not ends with int 1']       = $this->makeAssertionItem('param_decimal_string', 1);

        $list['param string Coração!# not ends with Cora'] = $this->makeAssertionItem('param_string', 'Cora');
        $list['param string Coração!# not ends with ção!'] = $this->makeAssertionItem('param_string', 'ção!');

        $list['param boolean false not ends with 1'] = $this->makeAssertionItem('param_false', '1');
        $list['param boolean false not ends with int 1'] = $this->makeAssertionItem('param_false', 1);
        $list['param boolean true not ends with 0'] = $this->makeAssertionItem('param_true', '2');
        $list['param boolean true not ends with int 0'] = $this->makeAssertionItem('param_true', 2);

        // array [
        //    111,    // inteiro
        //    '222',  // inteiro string
        //    22.5,   // decimal
        //    '11.5', // decimal string
        //    'ção!#' // string
        // ]

        // O array termina com 'ção!#'. Todos os outros elementos não valem
        $list["array not ends with integer 112"]         = $this->makeAssertionItem('param_array', 112);
        $list["array not ends with integer 112"]         = $this->makeAssertionItem('param_array', '112');
        $list["array not ends with integer string 222"]  = $this->makeAssertionItem('param_array', 221);
        $list["array not ends with integer string 222"]  = $this->makeAssertionItem('param_array', '221');
        $list["array not ends with decimal 22.5"]        = $this->makeAssertionItem('param_array', 22.4);
        $list["array not ends with decimal 22.5"]        = $this->makeAssertionItem('param_array', '22.4');
        $list["array not ends with decimal string 11.5"] = $this->makeAssertionItem('param_array', 11.4);
        $list["array not ends with decimal string 11.5"] = $this->makeAssertionItem('param_array', '11.4');

        // O array termina com 'ção!#', não com parte dele
        $list["array not ends with string ção!"] = $this->makeAssertionItem('param_array', 'ção!');

        $this->popHttpArrayParam(); // remove 'ção!#', agora termina com 11.5
        $list["array not ends with decimal string 11.4"] = $this->makeAssertionItem('param_array', '11.4');
        
        $this->popHttpArrayParam(); // remove 11.5, agora termina com 22.5
        $list["array not ends with decimal 22.4"] = $this->makeAssertionItem('param_array', 22.4);

        $this->popHttpArrayParam(); // remove 22.5, agora termina com 222
        $list["array not ends with integer string 221"] = $this->makeAssertionItem('param_array', '221');

        $this->popHttpArrayParam(); // remove 222, agora termina com 111
        $list["array not ends with integer 110"] = $this->makeAssertionItem('param_array', 110);
        
        return $list;
    }
}
