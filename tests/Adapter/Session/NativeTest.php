<?php

declare(strict_types=1);

namespace Tests\Adapter\Session;

use Iquety\Application\Adapter\Session\NativeSession;
use Iquety\Application\Http\Session;

class NativeTest extends AbstractCase
{
    protected function makeFactory(): Session
    {
        $session = new NativeSession();
        $session->enableTestMode();
        return $session;
    }
}
