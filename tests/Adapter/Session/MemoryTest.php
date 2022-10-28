<?php

declare(strict_types=1);

namespace Tests\Adapter\Session;

use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\Adapter\Session\SymfonyNativeSession;
use Iquety\Application\Http\Session;

class MemoryTest extends AbstractCase
{
    protected function makeFactory(): Session
    {
        return new MemorySession();
    }
}
