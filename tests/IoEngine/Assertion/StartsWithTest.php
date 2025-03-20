<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

class StartsWithTest extends AssertionCase
{
    use AssertionHasDefaultParams;
    use AssertionHasObjectValue;
    use AssertionHasFieldExists;

    public function setUpProvider(): void
    {
        $this->setAssertionMethod('startsWith');

        $this->setAssertionHttpParams($this->getDefaultHttpParams());
    }

    protected function shiftHttpArrayParam(): void
    {
        /** @var array<string,mixed> */
        $array = &$this->httpParamList['param_array'];

        array_shift($array);
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
        
        $list['param int 111 starts with int 111']    = $this->makeAssertionItem('param_int', 111);
        $list['param int 111 starts with string 111'] = $this->makeAssertionItem('param_int', '111');
        $list['param int 111 starts with int 11']     = $this->makeAssertionItem('param_int', 11);
        $list['param int 111 starts with string 11']  = $this->makeAssertionItem('param_int', '11');
        $list['param int 111 starts with int 1']      = $this->makeAssertionItem('param_int', 1);
        $list['param int 111 starts with string 1']   = $this->makeAssertionItem('param_int', '1');

        $list['param int string 222 starts with string 222'] = $this->makeAssertionItem('param_int_string', '222');
        $list['param int string 222 starts with string 22']  = $this->makeAssertionItem('param_int_string', '22');
        $list['param int string 222 starts with string 2']   = $this->makeAssertionItem('param_int_string', '2');
        $list['param int string 222 starts with int 222']    = $this->makeAssertionItem('param_int_string', 222);
        $list['param int string 222 starts with int 22']     = $this->makeAssertionItem('param_int_string', 22);
        $list['param int string 222 starts with int 2']      = $this->makeAssertionItem('param_int_string', 2);

        $list['param decimal 22.5 starts with string 22.5']  = $this->makeAssertionItem('param_decimal', '22.5');
        $list['param decimal 22.5 starts with decimal 22.5'] = $this->makeAssertionItem('param_decimal', 22.5);
        $list['param decimal 22.5 starts with string 22.']   = $this->makeAssertionItem('param_decimal', '22.');
        $list['param decimal 22.5 starts with float 22.']    = $this->makeAssertionItem('param_decimal', 22.);
        $list['param decimal 22.5 starts with string 22']    = $this->makeAssertionItem('param_decimal', '22');
        $list['param decimal 22.5 starts with float 22']     = $this->makeAssertionItem('param_decimal', 22);
        $list['param decimal 22.5 starts with string 2']     = $this->makeAssertionItem('param_decimal', '2');
        $list['param decimal 22.5 starts with float 2']      = $this->makeAssertionItem('param_decimal', 2);

        $list['param decimal 11.5 starts with string 11.5']  = $this->makeAssertionItem('param_decimal_string', '11.5');
        $list['param decimal 11.5 starts with decimal 11.5'] = $this->makeAssertionItem('param_decimal_string', 11.5);
        $list['param decimal 11.5 starts with string 11.']   = $this->makeAssertionItem('param_decimal_string', '11.');
        $list['param decimal 11.5 starts with float 11.']    = $this->makeAssertionItem('param_decimal_string', 11.);
        $list['param decimal 11.5 starts with string 11']    = $this->makeAssertionItem('param_decimal_string', '11');
        $list['param decimal 11.5 starts with int 11']       = $this->makeAssertionItem('param_decimal_string', 11);
        $list['param decimal 11.5 starts with string 1']     = $this->makeAssertionItem('param_decimal_string', '1');
        $list['param decimal 11.5 starts with int 1']        = $this->makeAssertionItem('param_decimal_string', 1);

        $list['param string Coração!# starts with Cora'] = $this->makeAssertionItem('param_string', 'Cora');

        $list['param boolean false starts with 0']     = $this->makeAssertionItem('param_false', '0');
        $list['param boolean false starts with int 0'] = $this->makeAssertionItem('param_false', 0);
        $list['param boolean true starts with 1']      = $this->makeAssertionItem('param_true', '1');
        $list['param boolean true starts with int 1']  = $this->makeAssertionItem('param_true', 1);

        // array [
        //    111,    // inteiro
        //    '222',  // inteiro string
        //    22.5,   // decimal
        //    '11.5', // decimal string
        //    'ção!#' // string
        // ]

        // inicia com 111
        $list["array starts with integer string 111"] = $this->makeAssertionItem('param_array', '111');
        $list["array starts with integer 111"]        = $this->makeAssertionItem('param_array', 111);

        $this->shiftHttpArrayParam(); // remove 111, agora inicia com '222'
        $list["array starts with integer string 222"] = $this->makeAssertionItem('param_array', '222');
        $list["array starts with integer 222"]        = $this->makeAssertionItem('param_array', 222);

        $this->shiftHttpArrayParam(); // remove '222', agora inicia com 22.5
        $list["array starts with decimal string 22.5"] = $this->makeAssertionItem('param_array', '22.5');
        $list["array starts with decimal 22.5"]        = $this->makeAssertionItem('param_array', 22.5);

        $this->shiftHttpArrayParam(); // remove 22.5, agora inicia com '11.5'
        $list["array starts with decimal string 11.5"] = $this->makeAssertionItem('param_array', '11.5');
        $list["array starts with decimal 11.5"]        = $this->makeAssertionItem('param_array', 11.5);

        $this->shiftHttpArrayParam(); // remove '11.5', agora inicia com 'ção!#'
        $list["array starts with ção!#"] = $this->makeAssertionItem('param_array', 'ção!#');
        
        return $list;
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $this->setUpProvider();

        $list = [];

        $list['param int 111 not starts with 112'] = $this->makeAssertionItem('param_int', 112);

        $list['param int string 222 not starts with string 223']         = $this->makeAssertionItem('param_int_string', '223');
        $list['param int string 222 not starts with integer string 223'] = $this->makeAssertionItem('param_int_string', 223);

        $list['param decimal 22.5 not starts with string 21.']  = $this->makeAssertionItem('param_decimal', '21.');
        $list['param decimal 22.5 not starts with string 21']   = $this->makeAssertionItem('param_decimal', '21');
        $list['param decimal 22.5 not starts with string 1']    = $this->makeAssertionItem('param_decimal', '1');
        $list['param decimal 22.5 not starts with decimal 21.'] = $this->makeAssertionItem('param_decimal', 21.);
        $list['param decimal 22.5 not starts with int 21']      = $this->makeAssertionItem('param_decimal', 21);
        $list['param decimal 22.5 not starts with int 1']       = $this->makeAssertionItem('param_decimal', 1);
        
        $list['param decimal 11.5 not starts with string 12.']  = $this->makeAssertionItem('param_decimal_string', '12.');
        $list['param decimal 11.5 not starts with string 12']   = $this->makeAssertionItem('param_decimal_string', '12');
        $list['param decimal 11.5 not starts with string 2']    = $this->makeAssertionItem('param_decimal_string', '2');
        $list['param decimal 11.5 not starts with decimal 12.'] = $this->makeAssertionItem('param_decimal_string', 12.);
        $list['param decimal 11.5 not starts with int 12']      = $this->makeAssertionItem('param_decimal_string', 12);
        $list['param decimal 11.5 not starts with int 2']       = $this->makeAssertionItem('param_decimal_string', 2);

        $list['param string Coração!# not starts with raç']  = $this->makeAssertionItem('param_string', 'raç');
        $list['param string Coração!# not starts with ção!'] = $this->makeAssertionItem('param_string', 'ção!');

        $list['param boolean false not starts with 1']     = $this->makeAssertionItem('param_false', '1');
        $list['param boolean false not starts with int 1'] = $this->makeAssertionItem('param_false', 1);
        $list['param boolean true not starts with 0']      = $this->makeAssertionItem('param_true', '2');
        $list['param boolean true not starts with int 0']  = $this->makeAssertionItem('param_true', 2);

        // array [
        //    111,    // inteiro
        //    '222',  // inteiro string
        //    22.5,   // decimal
        //    '11.5', // decimal string
        //    'ção!#' // string
        // ]

        // O array inicia com '111'. Todos os outros elementos não valem
        $list["array not starts with integer 112"]         = $this->makeAssertionItem('param_array', 112);
        $list["array not starts with integer string 112"]  = $this->makeAssertionItem('param_array', '112');
        $list["array not starts with integer 221"]         = $this->makeAssertionItem('param_array', 221);
        $list["array not starts with integer string 221"]  = $this->makeAssertionItem('param_array', '221');
        $list["array not starts with decimal 22.4"]        = $this->makeAssertionItem('param_array', 22.4);
        $list["array not starts with decimal string 22.4"] = $this->makeAssertionItem('param_array', '22.4');
        $list["array not starts with decimal 11.4"]        = $this->makeAssertionItem('param_array', 11.4);
        $list["array not starts with decimal string 11.4"] = $this->makeAssertionItem('param_array', '11.4');

        // O array inicia com 111, não com parte dele
        $list["array not starts with integer 11"]        = $this->makeAssertionItem('param_array', 11);
        $list["array not starts with integer string 11"] = $this->makeAssertionItem('param_array', '11');
        $list["array not starts with integer 1"]         = $this->makeAssertionItem('param_array', 1);
        $list["array not starts with integer string 1"]  = $this->makeAssertionItem('param_array', '1');

        $this->shiftHttpArrayParam(); // remove '111', agora inicia com '222'
        $list["array not starts with integer string 221"] = $this->makeAssertionItem('param_array', '221');
        $list["array not starts with integer 221"]        = $this->makeAssertionItem('param_array', 221);

        $this->shiftHttpArrayParam(); // remove '222', agora inicia com 22.5
        $list["array not starts with decimal string 22.4"] = $this->makeAssertionItem('param_array', '22.4');
        $list["array not starts with decimal 22.4"]        = $this->makeAssertionItem('param_array', 22.4);

        $this->shiftHttpArrayParam(); // remove 22.5, agora inicia com '11.5'
        $list["array not starts with decimal string 11.4"] = $this->makeAssertionItem('param_array', '11.4');
        $list["array not starts with decimal 11.4"]        = $this->makeAssertionItem('param_array', 11.4);

        $this->shiftHttpArrayParam(); // remove '11.5', agora inicia com 'ção!#'
        $list["array not starts with ão!#"] = $this->makeAssertionItem('param_array', 'ão!#');
        
        return $list;
    }
}
