<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\Http\HttpStatus;
use Psr\Http\Message\ResponseInterface;
use Tests\AppEngine\FrontController\Support\Commands\NoContractCommand;
use Tests\AppEngine\FrontController\Support\FcBootstrapConcrete;
use Tests\Unit\TestCase;

class FcOnlyExecuteTest extends TestCase
{
    // RuntimeException: This bootstrap has no directories registered as command source
    // teste compartilhado em tests/AppEngine/EngineTestCase.php

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function commandNotFound(string $httpFactoryContract): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, FcEngine::class);
        $request = $this->requestFactory($httpFactory, '/invalid');

        $engine->boot(new FcBootstrapConcrete());

        $response = $engine->execute($request, [], fn() => null);

        $this->assertNull($response);
    }

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function commandInvalidContract(string $httpFactoryContract): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, FcEngine::class);
        $request = $this->requestFactory($httpFactory, 'no/contract/command');

        $bootstrap = new FcBootstrapConcrete();

        $engine->boot($bootstrap);

        /** @var ResponseInterface $response */
        $response = $engine->execute(
            $request,
            [$bootstrap::class => &$bootstrap],
            function (FcBootstrap $bootstrap) {
            }
        );

        $this->assertEquals(HttpStatus::INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertStringContainsString(
            sprintf('Error: Class type %s is not allowed', NoContractCommand::class),
            (string)$response->getBody()
        );
    }
}
