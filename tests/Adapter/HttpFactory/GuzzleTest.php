<?php

declare(strict_types=1);

namespace Tests\Adapter\HttpFactory;

use Iquety\Application\Adapter\HttpFactory\GuzzleHttpFactory;
use Iquety\Application\Http\HttpFactory;

class GuzzleTest extends AbstractCase
{
    protected function makeFactory(): HttpFactory
    {
        return new GuzzleHttpFactory();
    }
}
