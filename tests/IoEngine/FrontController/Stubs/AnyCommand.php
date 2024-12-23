<?php

declare(strict_types=1);

namespace Tests\IoEngine\FrontController\Stubs;

use Iquety\Http\HttpMethod;

class AnyCommand extends CheckMethodCommand
{
    public function __construct()
    {
        $this->useMethod(HttpMethod::ANY);
    }
}
