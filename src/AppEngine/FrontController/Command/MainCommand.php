<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController\Command;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\Http\HttpFactory;
use Psr\Http\Message\ResponseInterface;

class MainCommand extends Command
{
    public function __construct()
    {
    }

    public function execute(): ResponseInterface
    {
        /** @var HttpFactory $factory */
        $factory = $this->make(HttpFactory::class);

        $response = $factory->createResponse(200);

        return $response->withBody($factory->createStream(
            'Iquety Framework'
        ));
    }
}
