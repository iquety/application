<?php

declare(strict_types=1);

namespace Tests\IoEngine\Stubs\FrontController;

use Iquety\Application\Http\HttpMethod;

class PostCommand extends CheckMethodCommand
{
    public function __construct()
    {
        $this->useMethod(HttpMethod::POST);
    }
}