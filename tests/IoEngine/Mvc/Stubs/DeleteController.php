<?php

declare(strict_types=1);

namespace Tests\IoEngine\Mvc\Stubs;

use Iquety\Http\HttpMethod;

class DeleteController extends CheckMethodController
{
    public function __construct()
    {
        $this->useMethod(HttpMethod::DELETE);
    }
}
