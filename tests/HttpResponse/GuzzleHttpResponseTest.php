<?php

declare(strict_types=1);

namespace Tests\HttpResponse;

use Iquety\Http\Adapter\HttpFactory\GuzzleHttpFactory;
use Iquety\Http\HttpFactory;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class GuzzleHttpResponseTest extends HttpResponseCase
{
    public function adapterFactory(): HttpFactory
    {
        return new GuzzleHttpFactory();
    }
}