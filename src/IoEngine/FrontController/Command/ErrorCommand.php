<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\FrontController\Command;

use Iquety\Application\IoEngine\Action\Input;
use Psr\Http\Message\ResponseInterface;

class ErrorCommand extends Command
{
    public function execute(): ResponseInterface
    {
        var_dump($this->make('ServerError'));
        exit;

        return $this->make('ServerError');
    }
}
