<?php

declare(strict_types=1);

namespace Tests\Http;

use Exception;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Application\Http\HttpStatus;
use Iquety\Injection\Container;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class DiactorosHttpResponseFactoryTest extends HttpResponseFactoryTestCase
{
    public function httpFactory(): HttpFactory
    {
        return new DiactorosHttpFactory();
    }
}
