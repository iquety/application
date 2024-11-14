<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use Iquety\Application\Application;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Action\Validable;
use Iquety\Shield\Shield;
use Tests\TestCase;

class AssertionCase extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
        Application::instance()->container()->addSingleton(Shield::class);
    }

    /** @return Input */
    protected function makeValidator(mixed $value): object
    {
        return new class($value)
        {
            use Validable;

            public function __construct(private mixed $fieldValue)
            {
            }

            public function param(): string
            {
                return $this->fieldValue;
            }
        };
    }
}
