<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\FrontController\Command;

use Iquety\Application\HttpResponseFactory;
use Iquety\Http\HttpStatus;
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
