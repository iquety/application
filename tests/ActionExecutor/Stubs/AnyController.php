<?php

declare(strict_types=1);

namespace Tests\ActionExecutor\Stubs;

use Iquety\Application\IoEngine\Mvc\Controller\Controller;

class AnyController extends Controller
{
    public function execute(): string
    {
        return 'Test';
    }
}
