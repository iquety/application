<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Mvc\Controller;

use Throwable;

class ErrorController extends Controller
{
    // error controlles não possui ioc
    public function execute(Throwable $exception): string
    {
        return $exception->getMessage();
    }
}
