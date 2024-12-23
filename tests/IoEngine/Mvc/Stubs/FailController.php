<?php

declare(strict_types=1);

namespace Tests\IoEngine\Mvc\Stubs;

use Exception;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;

class FailController extends Controller
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
        throw new Exception('Test Error');
    }
}
