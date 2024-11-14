<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use Iquety\Application\Application;
use Iquety\Application\IoEngine\Action\Validable;
use Iquety\Shield\Shield;
use LogicException;
use Tests\TestCase;

class ValidableTest extends AssertionCase
{
    /** @test */
    public function fluency(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('You need to start with the assert() method');

        $validator = $this->makeValidator('one');

        $validator->equalTo('one');
    }
}
