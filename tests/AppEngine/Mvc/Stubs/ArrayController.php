<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc\Stubs;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\Mvc\Controller\Controller;

class ArrayController extends Controller
{
    public function __construct()
    {
    }

    /** @SuppressWarnings(PHPMD.ShortVariable) */
    public function execute(Input $input, int $id): array
    {
        return [
            'Resposta com base em array',
            'Id ' . $id,
            'Input ' . $input
        ];
    }
}
