<?php

declare(strict_types=1);

namespace Tests\IoEngine\Mvc\Stubs;

use Iquety\Application\Http\HttpMethod;

class PostController extends CheckMethodController
{
    public function __construct()
    {
        $this->useMethod(HttpMethod::POST);
    }
}