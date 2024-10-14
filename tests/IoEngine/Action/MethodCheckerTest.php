<?php

declare(strict_types=1);

namespace Tests\IoEngine\Action;

use Iquety\Application\Application;
use Iquety\Application\IoEngine\Action\Makeable;
use Iquety\Application\IoEngine\Action\MethodChecker;
use Iquety\Application\IoEngine\Action\MethodNotAllowedException;
use Iquety\Http\HttpMethod;
use Psr\Http\Message\ServerRequestInterface;
use Tests\TestCase;

class MethodCheckerTest extends TestCase
{
    /** @return array<string,array<int,mixed>> */
    public function methodProvider(): array
    {
        $list = [];

        $list['delete']  = [HttpMethod::DELETE];
        // $list['get']   = [HttpMethod::GET];
        $list['patch'] = [HttpMethod::PATCH];
        $list['post']  = [HttpMethod::POST];
        $list['put']   = [HttpMethod::PUT];

        return $list;
    }

    /**
     * @test
     * @dataProvider methodProvider
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function methodBlocked(HttpMethod $method): void
    {
        $this->expectException(MethodNotAllowedException::class);
        $this->expectExceptionMessage('');

        $serveRequest = $this->makeHttpFactory()
            ->createServerRequest($method->value, '/', []);

        $application = Application::instance();
        $application
            ->container()
            ->addFactory(ServerRequestInterface::class, $serveRequest);

        $object = new class {
            use Makeable;
            use MethodChecker;

            public function execute(): void
            {
                $this->forMethod(HttpMethod::GET);
            }
        };

        $object->execute();
    }

    /**
     * @test
     * @dataProvider methodProvider
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function methodAllowed(HttpMethod $method): void
    {
        $serveRequest = $this->makeHttpFactory()
            ->createServerRequest($method->value, '/', []);

        $application = Application::instance();
        $application
            ->container()
            ->addFactory(ServerRequestInterface::class, $serveRequest);

        $object = new class {
            use Makeable;
            use MethodChecker;

            public function execute(): string
            {
                $this->forMethod(HttpMethod::ANY);

                return 'teste';
            }
        };

        $this->assertSame('teste', $object->execute());
    }
}
