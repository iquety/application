<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\Mvc\Stubs;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\Mvc\Controller\Controller;

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
