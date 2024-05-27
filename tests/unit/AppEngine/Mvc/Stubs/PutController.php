<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\Mvc\Stubs;

use Iquety\Application\Http\HttpMethod;

class PutController extends CheckMethodController
{
    public function __construct()
    {
        $this->useMethod(HttpMethod::PUT);
    }
}
