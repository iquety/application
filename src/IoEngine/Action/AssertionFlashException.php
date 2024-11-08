<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Action;

use Iquety\Shield\AssertionException;

class AssertionFlashException extends AssertionException
{
    private string $uri;

    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
