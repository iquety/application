<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\FrontController\Command;

use Iquety\Application\IoEngine\Action\Input;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ErrorCommand extends Command
{
    public function execute(Throwable $exception): string
    {
        return $exception->getMessage();
    }
}
