<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

trait HasProviderFieldNotExist
{
    /** @return array<string,array<int,mixed>> */
    public function invalidFieldExistsProvider(): array
    {
        $list = [];

        $list['field with value null does not exists']  = $this->makeAssertionItem('param_null', 'x');
        $list['field not declared does not exists'] = $this->makeAssertionItem('param_unknown', 'x');

        return $list;
    }
}
