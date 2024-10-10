<?php

declare(strict_types=1);

namespace Tests\Application;

use ArrayObject;
use InvalidArgumentException;
use Iquety\Application\Application;
use Iquety\Injection\NotFoundException;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ApplicationFactoriesTest extends TestCase
{
    /** @test */
    public function dependencyDefinition(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find dependency definition for identification');

        $application = Application::instance();

        $application->make('identification');
    }

    /** @test */
    public function makeInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Dependency id was not specified');

        $application = Application::instance();

        $application->make();
    }

    /** @test */
    public function factories(): void
    {
        $application = Application::instance();

        $application->container()->addFactory('one', 'value');
        $application->container()->addFactory('two', new ArrayObject());
        $application->container()->addFactory('three', ArrayObject::class);
        $application->container()->addFactory('four', fn() => new ArrayObject());
        $application->container()->addFactory('five', fn($value) => new ArrayObject($value));

        $this->assertSame('value', $application->make('one'));
        $this->assertEquals(new ArrayObject(), $application->make('two'));
        $this->assertEquals(new ArrayObject(), $application->make('three'));
        $this->assertEquals(new ArrayObject(), $application->make('four'));
        $this->assertEquals(
            new ArrayObject(['name' => 'ricardo']),
            $application->make('five', ['name' => 'ricardo'])
        );
    }
}
