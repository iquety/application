<?php

declare(strict_types=1);

namespace Tests\IoEngine\Stubs\FrontController;

use Iquety\Application\Http\HttpMethod;

class AnyCommand extends CheckMethodCommand
{
    public function __construct()
    {
        $this->useMethod(HttpMethod::ANY);
    }
}
