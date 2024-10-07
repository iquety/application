<?php

declare(strict_types=1);

namespace Tests\IoEngine\Mvc\Stubs;

use Iquety\Application\Http\HttpMethod;

class PutController extends CheckMethodController
{
    public function __construct()
    {
        $this->useMethod(HttpMethod::PUT);
    }
}