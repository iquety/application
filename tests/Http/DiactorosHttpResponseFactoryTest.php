<?php

declare(strict_types=1);

namespace Tests\Http;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Http\HttpFactory;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class DiactorosHttpResponseFactoryTest extends HttpResponseFactoryTestCase
{
    public function adapterFactory(): HttpFactory
    {
        return new DiactorosHttpFactory();
    }
}
