<?php

declare(strict_types=1);

namespace Tests\Adapter\HttpFactory;

use Freep\Application\Adapter\HttpFactory\GuzzleHttpFactory;
use Freep\Application\Http\HttpFactory;

class GuzzleTest extends AbstractCase
{
    protected function makeFactory(): HttpFactory
    {
        return new GuzzleHttpFactory();
    }
}
