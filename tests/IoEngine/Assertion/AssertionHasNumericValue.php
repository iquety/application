<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\Input;

trait AssertionHasNumericValue
{
    /** @return array<string,array<int,mixed>> */
    public function invalidNumericArgumentsProvider(): array
    {
        $this->setUpProvider();
        
        $this->setAssertionHttpParams([
            'param_string' => 'text',
            'param_array' => ['one', 'two'],
        ]);

        $list = [];

        $list['value string -> #1 needle object']  = $this->makeAssertionItem('param_string', 'xxx');
        $list['value array -> #1 needle object']   = $this->makeAssertionItem('param_array', 'xx');

        return $list;
    }

    /**
     * @test
     * @dataProvider invalidNumericArgumentsProvider
     */
    public function valueIsNotNumeric(
        string $queryString,
        string $assertionMethod,
        string $paramName,
        mixed $valueOne,
        mixed $valueTwo
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument must be numeric');

        $input = Input::fromString($queryString);

        $input->assert($paramName)->$assertionMethod($valueOne, $valueTwo);
        
        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
