<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController\Command;

use Iquety\Application\AppEngine\Action\Input;
use Psr\Http\Message\ResponseInterface;

class ErrorCommand extends Command
{
    public function execute(): ResponseInterface
    {
        return $this->make('ErrorResponse');
    }
}
