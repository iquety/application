<?php

declare(strict_types=1);

namespace Tests\IoEngine\Assertion;

use Iquety\Application\Application;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Shield\Shield;
use LogicException;
use Tests\TestCase;

class ValidableTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
        Application::instance()->container()->addSingleton(Shield::class);
    }

    public function tearDown(): void
    {
        Application::instance()->reset();
    }
    
    /** @test */
    public function invalidStart(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('You need to start with the assert() method');

        $input = Input::fromString('/user?' . http_build_query(['name' => 'xxxxx']));

        $input->equalTo('name');
    }

    /** @test */
    public function fluency(): void
    {
        $input = Input::fromString('/user?' . http_build_query(['name' => 'xxxxx']));

        $input->assert('name')
            ->equalTo('xxxxx')->message('You need to')
            ->contains('x')->message('You need to')

            ->assert('name')
            ->equalTo('xxxxx')
            ->contains('x')->message('You need to');

        $input->assert('name')
            ->equalTo('xxxxx')->message('You need to')
            ->contains('x');

        $this->assertTrue(true);
    }
}
