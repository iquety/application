<?php

declare(strict_types=1);

namespace Tests\Adapter\Session;

use Freep\Application\Adapter\Session\MemorySession;
use Freep\Application\Adapter\Session\SymfonyNativeSession;
use Freep\Application\Http\Session;

class SymfonyNativeTest extends AbstractCase
{
    protected function makeFactory(): Session
    {
        $session = new SymfonyNativeSession();
        $session->enableMode();
        return $session;
    }
}
