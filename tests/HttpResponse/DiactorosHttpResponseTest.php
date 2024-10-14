<?php

declare(strict_types=1);

namespace Tests\HttpResponse;

use Iquety\Http\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Http\HttpFactory;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class DiactorosHttpResponseTest extends HttpResponseCase
{
    public function adapterFactory(): HttpFactory
    {
        return new DiactorosHttpFactory();
    }
}