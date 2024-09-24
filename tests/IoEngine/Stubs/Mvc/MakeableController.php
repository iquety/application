<?php

declare(strict_types=1);

namespace Tests\IoEngine\Stubs\Mvc;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;
use Iquety\Application\Http\HttpMethod;
use Psr\Http\Message\ServerRequestInterface;

class MakeableController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @return array<int,string>
     */
    public function execute(Input $input): string
    {
        $this->make(ServerRequestInterface::class);
        
        return 'ok';
    }
}
