<?php

declare(strict_types=1);

namespace Tests\IoEngine\Mvc\Stubs;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;
use Iquety\Http\HttpMethod;

class MethodPostController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return array<int,string>
     */
    public function execute(Input $input): array
    {
        $this->forMethod(HttpMethod::POST);

        return [];
    }
}
