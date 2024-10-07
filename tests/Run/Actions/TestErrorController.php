<?php

declare(strict_types=1);

namespace Tests\Run\Actions;

use Exception;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;

class TestErrorController extends Controller
{
    public function myMethod(): string
    {
        throw new Exception('Mensagem de erro');
    }
}
