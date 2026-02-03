<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

trait HasProviderNonNumericValue
{
    /** @return array<string,array<int,mixed>> */
    public function invalidNonNumericArgumentsProvider(): array
    {
        $list = [];

        // 'param_empty_string'   => '',
        // 'param_int'            => 111,           // inteiro
        // 'param_int_string'     => '222',         // inteiro string
        // 'param_decimal'        => 22.5,          // decimal
        // 'param_decimal_zero'   => 22.0,          // decimal
        // 'param_decimal_string' => '11.5',        // decimal string
        // 'param_string'         => 'Coração!#',   // string
        // 'param_null'           => null,          // nulos são removidos por http_build_query
        // 'param_false'          => false,         // false é mudado para 0
        // 'param_true'           => true,          // true é mudado para 1
        // 'param_array'          => [

        $list['value empty string -> #1 needle numeric']  = $this->makeAssertionItem('param_empty_string', 'xxx');
        $list['value string -> #1 needle numeric']  = $this->makeAssertionItem('param_string', 'xxx');
        $list['value array -> #1 needle numeric']   = $this->makeAssertionItem('param_array', 'xx');

        return $list;
    }
}
