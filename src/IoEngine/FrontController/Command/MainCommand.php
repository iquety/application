<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\FrontController\Command;

use Iquety\Http\HttpFactory;
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
            'Iquety Framework - Home Page'
        ));
    }
}
