<?php

declare(strict_types=1);

namespace Tests\IoEngine\FrontController\Stubs;

use Iquety\Application\Http\HttpMethod;

class GetCommand extends CheckMethodCommand
{
    public function __construct()
    {
        $this->useMethod(HttpMethod::GET);
    }
}