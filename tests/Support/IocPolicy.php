<?php

declare(strict_types=1);

namespace Tests\Support;

use Iquety\Application\Http\Session;
use Iquety\Application\Routing\Policy;

class IocPolicy implements Policy
{
    public function __construct(private Session $session)
    {
    }

    public function check(): bool
    {
        return $this->session->has('allow');
    }
}
