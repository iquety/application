<?php

declare(strict_types=1);

namespace Tests\IoEngine\Stubs\Mvc;

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
     * @return array<int,string>
     */
    public function execute(Input $input): array
    {
        throw new Exception('Test Error');
    }
}
