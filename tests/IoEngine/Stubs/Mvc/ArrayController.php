<?php

declare(strict_types=1);

namespace Tests\IoEngine\Stubs\Mvc;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;

class ArrayController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @return array<int,string>
     */
    public function execute(Input $input, int $id): array
    {
        return [
            'Resposta com base em array',
            'Id ' . $id,
            'Input ' . $input
        ];
    }
}
