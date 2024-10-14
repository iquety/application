<?php

declare(strict_types=1);

namespace Tests\HttpResponse;

use Iquety\Http\Adapter\HttpFactory\NyHolmHttpFactory;
use Iquety\Http\HttpFactory;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class NyHolmHttpResponseTest extends HttpResponseCase
{
    public function adapterFactory(): HttpFactory
    {
        return new NyHolmHttpFactory();
    }
}
