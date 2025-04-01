<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

trait HasProviderNumericValue
{
    /** @return array<string,array<int,mixed>> */
    public function invalidNumericArgumentsProvider(): array
    {
        $list = [];

        $list['value string -> #1 needle object']  = $this->makeAssertionItem('param_string', 'xxx');
        $list['value array -> #1 needle object']   = $this->makeAssertionItem('param_array', 'xx');

        return $list;
    }
}
