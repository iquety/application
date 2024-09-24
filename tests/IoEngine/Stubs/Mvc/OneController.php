<?php

declare(strict_types=1);

namespace Tests\IoEngine\Stubs\Mvc;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;

class OneController extends Controller
{
    public function __construct()
    {
    }

    /** @SuppressWarnings(PHPMD.ShortVariable) */
    public function execute(Input $input, int $id): string
    {
        return 'Resposta do controlador para id ' . $id . ' input ' . $input;
    }
}
