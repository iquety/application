<?php

declare(strict_types=1);

namespace Tests\AppEngine;

use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\Http\HttpFactory;

class FcEngineTest extends EngineTestCase
{
    protected function engineFactory(HttpFactory $httpFactory): AppEngine
    {
        return $this->appEngineFactory($httpFactory, FcEngine::class);
    }
}
