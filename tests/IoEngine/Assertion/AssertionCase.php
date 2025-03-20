<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use InvalidArgumentException;
use Iquety\Application\Application;
use Iquety\Application\IoEngine\Action\AssertionResponseException;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Action\Validable;
use Iquety\Shield\Shield;
use Tests\TestCase;

abstract class AssertionCase extends TestCase
{
    protected string $assertionMethod = '';

    /** @var array<string,float|int|string> */
    protected array $httpParamList = [];

    public function setUp(): void
    {
        Application::instance()->reset();
        Application::instance()->container()->addSingleton(Shield::class);
    }

    public function tearDown(): void
    {
        Application::instance()->reset();
    }

    abstract public function setUpProvider(): void;

    /** @param array<string,float|int|string> $paramList */
    protected function setAssertionHttpParams(array $paramList): void
    {
        $this->httpParamList = $paramList;
    }

    protected function setAssertionMethod(string $methodName): void
    {
        $this->assertionMethod = $methodName;
    }

    protected function makeAssertionItem(
        string $paramName,
        mixed $valueOne = '',
        mixed $valueTwo = ''
    ): array {
        return [
            '/user/edit/03?' . http_build_query($this->httpParamList),
            $this->assertionMethod,
            $paramName,
            $valueOne,
            $valueTwo
        ];
    }

    /**
     * Fabrica uma imitação de Iquety\Application\IoEngine\Action\Input;
     * @return Input */
    protected function makeObjectWithValidator(mixed $value): object
    {
        return new class($value)
        {
            use Validable;

            public function __construct(private mixed $fieldValue)
            {
            }

            public function param(): mixed
            {
                return $this->fieldValue;
            }
        };
    }


    /**
     * @test
     * @dataProvider validProvider
     */
    public function valueAsserted(
        string $queryString,
        string $assertionMethod,
        string $paramName,
        mixed $valueOne,
        mixed $valueTwo
    ): void {
        $input = Input::fromString($queryString);

        $input->assert($paramName)->$assertionMethod($valueOne, $valueTwo);

        // se a asserção não passar, uma exceção será lançada
        $input->validOrResponse();

        // se chegar até aqui... tudo correu bem
        $this->assertTrue(true);
    }

    /**
     * Recebe um valor (texto, inteiro ou decimal) transformado em texto 
     * Compara com um valor (texto, inteiro ou decimal) transformado em texto
     * @test
     * @dataProvider invalidProvider
     */
    public function valueNotAsserted(
        string $queryString,
        string $assertionMethod,
        string $paramName,
        mixed $valueOne,
        mixed $valueTwo
    ): void {
        $this->expectException(AssertionResponseException::class);
        $this->expectExceptionMessage('The value was not successfully asserted');

        $input = Input::fromString($queryString);

        $input->assert($paramName)->$assertionMethod($valueOne, $valueTwo);

        // se a asserção não passar, uma exceção será lançada
        // para o ActionExecutor capturar e liberar a resposta
        $input->validOrResponse();
    }
}
