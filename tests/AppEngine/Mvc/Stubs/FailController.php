<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc\Stubs;

use Exception;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\Mvc\Controller\Controller;

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
