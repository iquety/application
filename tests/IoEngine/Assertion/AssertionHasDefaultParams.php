<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

trait AssertionHasDefaultParams
{
    /** @return array<string,array<int,mixed>> */
    public function getDefaultHttpParams(): array
    {
        return [
            'param_empty_string'   => '',
            'param_int'            => 111,           // inteiro
            'param_int_string'     => '222',         // inteiro string
            'param_decimal'        => 22.5,          // decimal
            'param_decimal_string' => '11.5',        // decimal string
            'param_string'         => 'Coração!#',   // string
            'param_null'           => null,          // nulos são removidos por http_build_query
            'param_false'          => false,         // false é mudado para 0
            'param_true'           => true,          // true é mudado para 1
            'param_array'          => [
                111,    // inteiro
                '222',  // inteiro string
                22.5,   // decimal
                '11.5', // decimal string
                'ção!#' // string
            ]
        ];
    }
}
