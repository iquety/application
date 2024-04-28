<?php

declare(strict_types=1);

namespace Tests\Unit\Adapter\HttpFactory;

use Iquety\Application\Adapter\HttpFactory\NyHolmHttpFactory;
use Iquety\Application\Http\HttpFactory;

class NyHolmTest extends AbstractCase
{
    protected function makeFactory(): HttpFactory
    {
        return new NyHolmHttpFactory();
    }
}
