<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController\Stubs\Commands;

use Iquety\Application\Http\HttpMethod;

class DeleteCommand extends CheckMethodCommand
{
    public function __construct()
    {
        $this->useMethod(HttpMethod::DELETE);
    }
}
