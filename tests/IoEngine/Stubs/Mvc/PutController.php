<?php

declare(strict_types=1);

namespace Tests\IoEngine\Stubs\Mvc;

use Iquety\Application\Http\HttpMethod;

class PutController extends CheckMethodController
{
    public function __construct()
    {
        $this->useMethod(HttpMethod::PUT);
    }
}
