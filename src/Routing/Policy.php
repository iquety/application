<?php

declare(strict_types=1);

namespace Freep\Application\Routing;

interface Policy
{
    public function check(): bool;
}
