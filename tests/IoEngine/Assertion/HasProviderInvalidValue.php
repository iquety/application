<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use stdClass;

trait HasProviderInvalidValue
{
    /** @return array<string,array<int,mixed>> */
    public function invalidObjectArgumentsProvider(): array
    {
        $list = [];

        $list['value string -> #1 needle object']  = $this->makeAssertionItem('param_string', new stdClass());
        $list['value integer -> #1 needle object'] = $this->makeAssertionItem('param_int', new stdClass());
        $list['value float -> #1 needle object']   = $this->makeAssertionItem('param_float', new stdClass());
        $list['value array -> #1 needle object']   = $this->makeAssertionItem('param_array', new stdClass());

        return $list;
    }
}
