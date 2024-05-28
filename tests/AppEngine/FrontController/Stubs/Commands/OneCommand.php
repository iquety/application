<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController\Stubs\Commands;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\FrontController\Command\Command;
use Iquety\Application\Http\HttpFactory;
use Psr\Http\Message\ResponseInterface;

class OneCommand extends Command
{
    public function __construct()
    {
    }

    public function execute(Input $input): ResponseInterface
    {
        /** @var HttpFactory $factory */
        $factory = $this->make(HttpFactory::class);

        $response = $factory->createResponse(201);

        return $response->withBody($factory->createStream((string)$input));
    }
}
