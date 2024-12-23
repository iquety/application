<?php

declare(strict_types=1);

namespace Tests\IoEngine\FrontController\Stubs;

use Iquety\Http\HttpMethod;

class DeleteCommand extends CheckMethodCommand
{
    public function __construct()
    {
        $this->useMethod(HttpMethod::DELETE);
    }
}
