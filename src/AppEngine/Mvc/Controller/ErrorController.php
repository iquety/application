<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Mvc\Controller;

use Iquety\Application\AppEngine\Action\Input;
use Psr\Http\Message\ResponseInterface;

class ErrorController extends Controller
{
    public function execute(Input $input): ResponseInterface
    {
        return $this->make('ErrorResponse');
    }
}

