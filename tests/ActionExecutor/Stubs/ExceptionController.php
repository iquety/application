<?php

declare(strict_types=1);

namespace Tests\ActionExecutor\Stubs;

use Exception;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;

class ExceptionController extends Controller
{
    public function execute(): string
    {
        throw new Exception('Error message');
    }
}
