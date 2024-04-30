<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController\Command;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\Http\HttpFactory;
use Psr\Http\Message\ResponseInterface;

class ErrorCommand extends Command
{
    public function __construct()
    {
    }

    public function execute(Input $input): ResponseInterface
    {
        /** @var ResponseInterface */
        return $this->make(HttpFactory::class)->createResponse();
    }
}
