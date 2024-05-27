<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use Iquety\Application\Adapter\HttpFactory\NyHolmHttpFactory;
use Iquety\Application\Http\HttpFactory;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class NyHolmHttpResponseFactoryTest extends HttpResponseFactoryTestCase
{
    public function adapterFactory(): HttpFactory
    {
        return new NyHolmHttpFactory();
    }
}
