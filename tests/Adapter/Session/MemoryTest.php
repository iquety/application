<?php

declare(strict_types=1);

namespace Tests\Adapter\Session;

use Freep\Application\Adapter\Session\MemorySession;
use Freep\Application\Adapter\Session\SymfonyNativeSession;
use Freep\Application\Http\Session;

class MemoryTest extends AbstractCase
{
    protected function makeFactory(): Session
    {
        return new MemorySession();
    }
}
