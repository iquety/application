<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc\Stubs;

use Exception;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\Mvc\Controller\Controller;
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
