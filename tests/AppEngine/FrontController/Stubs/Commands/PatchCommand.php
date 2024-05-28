<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController\Stubs\Commands;

use Iquety\Application\Http\HttpMethod;

class PatchCommand extends CheckMethodCommand
{
    public function __construct()
    {
        $this->useMethod(HttpMethod::PATCH);
    }
}
