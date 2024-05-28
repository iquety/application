<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc\Stubs;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\Mvc\Controller\Controller;

class StringController extends Controller
{
    public function __construct()
    {
    }

    /** @SuppressWarnings(PHPMD.ShortVariable) */
    public function execute(Input $input, int $id): string
    {
        return 'Resposta com base em texto para id ' . $id . ' input ' . $input;
    }
}
