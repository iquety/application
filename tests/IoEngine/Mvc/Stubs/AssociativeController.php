<?php

declare(strict_types=1);

namespace Tests\IoEngine\Mvc\Stubs;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;

class AssociativeController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @return array<string,mixed>
     */
    public function execute(Input $input, int $id): array
    {
        return [
            'message' => 'Resposta com base em array',
            'id'      => $id,
            'input'   => (string)$input
        ];
    }
}
