<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\Mvc;

use Iquety\Application\AppEngine\Mvc\MvcEngine;
use RuntimeException;
use Tests\Unit\TestCase;

class MvcOnlyBootTest extends TestCase
{
    /**
     * @test
     * @dataProvider httpFactoryProvider
     */
    public function bootstrapWithoutRegisterRoutes(string $httpFactoryContract): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("There are no registered routes");

        $httpFactory = $this->httpFactory($httpFactoryContract);

        $engine = $this->appEngineFactory($httpFactory, MvcEngine::class);

        $request = $this->requestFactory($httpFactory);
        $moduleList = [];
        $bootDependencies = fn() => null;

        $response = $engine->execute($request, $moduleList, $bootDependencies);

        $this->assertNull($response);
    }
}
