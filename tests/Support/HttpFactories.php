<?php

declare(strict_types=1);

namespace Tests\Support;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpResponseFactory;
use Psr\Http\Message\ServerRequestInterface;

trait HttpFactories
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function makeHttpFactory(): HttpFactory
    {
        return new DiactorosHttpFactory();
    }

    protected function makeResponseFactory(): HttpResponseFactory
    {
        return new HttpResponseFactory(
            $this->makeHttpFactory(),
            $this->makeServerRequest()
        );
    }

    protected function makeServerRequest(): ServerRequestInterface
    {
        return $this->makeHttpFactory()->createRequestFromGlobals();
    }
}
