<?php

declare(strict_types=1);

namespace Tests\AppEngine;

use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\Http\HttpFactory;
use RuntimeException;
use Tests\TestCase;

abstract class EngineTestCase extends TestCase
{
    abstract protected function engineFactory(HttpFactory $httpFactory): AppEngine;

    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function bootstrapWithoutRegisterInputs(string $httpFactoryContract): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches("/This bootstrap has .*/");

        $httpFactory = $this->httpFactory($httpFactoryContract);

        $engine = $this->engineFactory($httpFactory);

        $request = $this->requestFactory($httpFactory);
        $moduleList = [];
        $bootDependencies = function () {
        };

        $response = $engine->execute($request, $moduleList, $bootDependencies);

        $this->assertNull($response);
    }
}
