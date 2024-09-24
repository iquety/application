<?php

declare(strict_types=1);

namespace Tests\IoEngine\Stubs\Mvc;

use Iquety\Application\Http\HttpMethod;

class AnyController extends CheckMethodController
{
    public function __construct()
    {
        $this->useMethod(HttpMethod::ANY);
    }
}
