<?php

declare(strict_types=1);

namespace Iquety\Application\Routing;

interface Policy
{
    public function check(): bool;
}
