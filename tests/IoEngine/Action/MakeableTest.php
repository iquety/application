<?php

declare(strict_types=1);

namespace Tests\IoEngine\Action;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpMethod;
use Iquety\Application\IoEngine\Action\Makeable;
use Iquety\Application\IoEngine\Action\MethodChecker;
use Iquety\Application\IoEngine\Action\MethodNotAllowedException;
use Psr\Http\Message\ServerRequestInterface;
use Tests\TestCase;

class MakeableTest extends TestCase
{
    /** @test */
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
