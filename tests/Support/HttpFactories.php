<?php

declare(strict_types=1);

namespace Tests\Support;

use Iquety\Application\Environment;
use Iquety\Application\HttpResponseFactory;
use Iquety\Http\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Http\HttpFactory;
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
            $this->makeServerRequest(),
            Environment::STAGE
        );
    }

    protected function makeServerRequest(): ServerRequestInterface
    {
        return $this->makeHttpFactory()->createRequestFromGlobals();
    }
}
