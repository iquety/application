<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\Input;
use stdClass;

trait AssertionHasObjectValue
{
    /** @return array<string,array<int,mixed>> */
    public function invalidObjectArgumentsProvider(): array
    {
        $this->setUpProvider();
        
        $this->setAssertionHttpParams([
            'param_string' => 'text',
            'param_int' => 123,
            'param_float' => 12.3,
            'param_array' => ['one', 'two'],
        ]);

        $list = [];

        $list['value string -> #1 needle object']  = $this->makeAssertionItem('param_string', new stdClass());
        $list['value integer -> #1 needle object'] = $this->makeAssertionItem('param_int', new stdClass());
        $list['value float -> #1 needle object']   = $this->makeAssertionItem('param_float', new stdClass());
        $list['value array -> #1 needle object']   = $this->makeAssertionItem('param_array', new stdClass());

        return $list;
    }
    
    /**
     * @test
     * @dataProvider invalidObjectArgumentsProvider
     */
    public function valueIsInvalidObject(
        string $queryString,
        string $assertionMethod,
        string $paramName,
        mixed $valueOne,
        mixed $valueTwo
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument is not valid');

        $input = Input::fromString($queryString);

        $input->assert($paramName)->$assertionMethod($valueOne, $valueTwo);
        
        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
