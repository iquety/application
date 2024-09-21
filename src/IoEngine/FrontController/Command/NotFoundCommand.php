<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\FrontController\Command;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\Http\HttpFactory;
use Psr\Http\Message\ResponseInterface;

class NotFoundCommand extends Command
{
    public function __construct()
    {
    }

    public function execute(): ResponseInterface
    {
        /** @var HttpFactory $factory */
        $factory = $this->make(HttpFactory::class);

        return $factory->createResponse(404);
    }
}
