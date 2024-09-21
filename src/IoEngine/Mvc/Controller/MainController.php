<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Mvc\Controller;

class MainController extends Controller
{
    public function execute(): string
    {
        return 'Iquety Framework - Home Page';
    }
}
