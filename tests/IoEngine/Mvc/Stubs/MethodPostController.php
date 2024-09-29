<?php

declare(strict_types=1);

namespace Tests\IoEngine\Mvc\Stubs;

use Exception;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;
use Iquety\Application\Http\HttpMethod;
use Iquety\Application\Http\HttpMime;

class MethodPostController extends Controller
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
        $this->forMethod(HttpMethod::POST);
        
        return [];
    }
}
