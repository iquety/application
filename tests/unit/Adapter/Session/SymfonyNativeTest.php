<?php

declare(strict_types=1);

namespace Tests\Unit\Adapter\Session;

use Iquety\Application\Adapter\Session\SymfonyNativeSession;
use Iquety\Application\Http\Session;

class SymfonyNativeTest extends AbstractCase
{
    protected function makeFactory(): Session
    {
        $session = new SymfonyNativeSession();
        $session->enableTestMode();
        return $session;
    }
}
