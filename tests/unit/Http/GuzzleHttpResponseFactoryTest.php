<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use Iquety\Application\Adapter\HttpFactory\GuzzleHttpFactory;
use Iquety\Application\Http\HttpFactory;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class GuzzleHttpResponseFactoryTest extends HttpResponseFactoryTestCase
{
    public function adapterFactory(): HttpFactory
    {
        return new GuzzleHttpFactory();
    }
}
