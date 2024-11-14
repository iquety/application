<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

class ContainsTest extends AssertionCase
{
    /** @return array<string,array<int,mixed>> */
    public function validProvider(): array
    {
        $list = [];

        $list['string'] = ['Palavra', 'lavr'];
        $list['numeric'] = ['123456', '345'];
        $list['float'] = ['12.34', '.34'];

        return $list;
    }

    /**
     * @test
     * @dataProvider validProvider
     */
    public function assertionValid(string $fieldValue, string $needle): void
    {
        $validator = $this->makeValidator($fieldValue);

        $validator->assert('nome')->contains($needle);

        // se a asserção não passar, uma exceção será lançada
        $validator->validOrResponse();

        $this->assertTrue(true);
    }

    /** @return array<string,array<int,mixed>> */
    public function invalidProvider(): array
    {
        $list = [];

        $list['string'] = ['Palavra', 'lavr'];
        $list['numeric'] = ['123456', '345'];
        $list['float'] = ['12.34', '.34'];

        return $list;
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function assertionInValid(string $fieldValue, string $needle): void
    {
        $validator = $this->makeValidator($fieldValue);

        $validator->assert('nome')->contains($needle);

        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $validator->validOrResponse();
    }
}
