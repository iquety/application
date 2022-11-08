<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\Http\HttpStatus;
use Tests\AppEngine\FrontController\Support\Commands\NoContractCommand;
use Tests\AppEngine\FrontController\Support\Commands\UserCommand;
use Tests\AppEngine\FrontController\Support\FcBootstrapConcrete;
use Tests\TestCase;

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

        $response = $engine->execute(
            $request,
            [$bootstrap::class => &$bootstrap],
            function(FcBootstrap $bootstrap) {}
        );

        $this->assertEquals(HttpStatus::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertStringContainsString(
            sprintf('Error: Class type %s is not allowed', NoContractCommand::class),
            (string)$response->getBody()
        );
    }

    /** 
     * @test 
     * @dataProvider httpFactoryProvider
     */
    public function commandOk(string $httpFactoryContract): void
    {
        $httpFactory = $this->httpFactory($httpFactoryContract);
        $engine = $this->appEngineFactory($httpFactory, FcEngine::class);
        $request = $this->requestFactory($httpFactory, 'user/command/42');

        $bootstrap = new FcBootstrapConcrete();

        $engine->boot($bootstrap);

        $response = $engine->execute(
            $request,
            [$bootstrap::class => &$bootstrap],
            function(FcBootstrap $bootstrap) {}
        );

        $this->assertEquals(HttpStatus::HTTP_OK, $response->getStatusCode());
        $this->assertStringContainsString(
            'Resposta do comando para id 42',
            (string)$response->getBody()
        );
    }
}
