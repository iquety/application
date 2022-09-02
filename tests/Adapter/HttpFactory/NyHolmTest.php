<?php

declare(strict_types=1);

namespace Tests\Adapter\HttpFactory;

use Freep\Application\Adapter\HttpFactory\NyHolmHttpFactory;
use Freep\Application\Http\HttpFactory;

class NyHolmTest extends AbstractCase
{
    protected function makeFactory(): HttpFactory
    {
        return new NyHolmHttpFactory();
    }
}
