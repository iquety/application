<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use Iquety\Application\Application;
use Iquety\Shield\Shield;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.NumberOfChildren) */
abstract class AssertionCase extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
        Application::instance()->container()->addSingleton(Shield::class);
    }

    public function tearDown(): void
    {
        Application::instance()->reset();
    }

    /** @return array<string,array<int,float|int|string>|bool|float|int|string|null> */
    public function getHttpParams(): array
    {
        return [
            'param_empty_string'   => '',
            'param_int'            => 111,           // inteiro
            'param_int_string'     => '222',         // inteiro string
            'param_decimal'        => 22.5,          // decimal
            'param_decimal_zero'   => 22.0,          // decimal
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

    protected function paramToLabel(string $param): string
    {
        $label = explode('_', $param);
        $label = array_map(fn($word) => ucfirst($word), $label);
        $label = implode(' ', $label);

        return $label;
    }

    /** @return array<int,mixed> */
    protected function makeAssertionItem(string $paramName, mixed $valueOne = '', mixed $valueTwo = ''): array
    {
        return [
            $paramName,
            $valueOne,
            $valueTwo
        ];
    }
}
