<?php

declare(strict_types=1);

namespace Tests\ActionExecutor\Stubs;

use Iquety\Application\IoEngine\Action\MethodNotAllowedException;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;

class MethodNotController extends Controller
{
    public function execute(): string
    {
        throw new MethodNotAllowedException('');
    }
}
