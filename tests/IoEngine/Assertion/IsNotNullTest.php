<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use Iquety\Application\IoEngine\Action\Input;
use RuntimeException;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class IsNotNullTest extends AssertionCase
{
    /** @test */
    public function invalidMethod(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Method isNotNull does not exist");

        $input = Input::fromString(
            '/user/edit/03?' . http_build_query(['param' => 'x']),
        );

        $input->assert('param')->isNotNull();
    }
}
