<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\FrontController\Command;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Application\Http\HttpStatus;
use Psr\Http\Message\ResponseInterface;

class NotFoundCommand extends Command
{
    public function __construct()
    {
    }

    public function execute(): ResponseInterface
    {
        /** @var HttpResponseFactory $factory */
        $factory = $this->make(HttpResponseFactory::class);

        return $factory->response('Not Found', HttpStatus::NOT_FOUND);
    }
}
