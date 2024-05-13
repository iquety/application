<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController\Command;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\Http\HttpFactory;
use Psr\Http\Message\ResponseInterface;

class NotFoundCommand extends Command
{
    public function __construct()
    {
    }

    public function execute(Input $input): ResponseInterface
    {
        /** @var HttpFactory $factory */
        $factory = $this->make(HttpFactory::class);

        return $factory->createResponse(404);
    }
}
