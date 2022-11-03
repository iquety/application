<?php

declare(strict_types=1);

namespace Tests\AppEngine;

use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Http\HttpFactory;

class MvcEngineTest extends EngineTestCase
{
    protected function engineFactory(HttpFactory $httpFactory): AppEngine
    {
        return $this->appEngineFactory($httpFactory, MvcEngine::class);
    }
}
