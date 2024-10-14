<?php

declare(strict_types=1);

namespace Tests\IoEngine\FrontController\Stubs;

use Exception;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\FrontController\Command\Command;
use Iquety\Http\HttpFactory;
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

        $response = $factory->createResponse(404);

        return $response->withBody($factory->createStream((string)$input));
    }
}
