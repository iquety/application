<?php

declare(strict_types=1);

namespace Tests\Run\Actions;

use Iquety\Application\IoEngine\Mvc\Controller\Controller;

class TestMainController extends Controller
{
    public function myMethod(): string
    {
        return 'Custom home page';
    }
}

