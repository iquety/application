<?php

declare(strict_types=1);

namespace Tests\Adapter\HttpFactory;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Http\HttpFactory;

class DiactorosTest extends AbstractCase
{
    protected function makeFactory(): HttpFactory
    {
        return new DiactorosHttpFactory();
    }
}
