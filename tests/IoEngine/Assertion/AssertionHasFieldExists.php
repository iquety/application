<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\Input;

trait AssertionHasFieldExists
{
    /** @return array<string,array<int,mixed>> */
    public function invalidFieldExistsProvider(): array
    {
        $this->setUpProvider();
        
        $this->setAssertionHttpParams([
            'param_null' => null,
        ]);

        $list = [];

        $list['field with value null does not exists']  = $this->makeAssertionItem('param_null', 'x');
        $list['field not declared does not exists'] = $this->makeAssertionItem('param_unknown', 'x');

        return $list;
    }
    
    /**
     * @test
     * @dataProvider invalidFieldExistsProvider
     */
    public function fieldDoesNotExist(
        string $queryString,
        string $assertionMethod,
        string $paramName
    ): void {
        $this->setUpProvider();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Field '$paramName' does not exist");

        $assertionMethod = $this->assertionMethod;

        $input = Input::fromString($queryString);

        $input->assert($paramName)->$assertionMethod('xx', 'xx');
        
        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
