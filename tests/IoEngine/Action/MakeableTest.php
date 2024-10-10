<?php

declare(strict_types=1);

namespace Tests\IoEngine\Action;

use Iquety\Application\Application;
use Iquety\Application\IoEngine\Action\Makeable;
use Tests\TestCase;

class MakeableTest extends TestCase
{
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function makeable(): void
    {
        $application = Application::instance();
        $application
            ->container()
            ->addFactory('dependency', 'teste');

        $object = new class {
            use Makeable;

            public function execute(): string
            {
                return $this->make('dependency');
            }
        };

        $this->assertSame('teste', $object->execute());
    }
}
