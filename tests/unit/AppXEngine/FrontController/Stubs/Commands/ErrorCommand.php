<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController\Stubs\Commands;

use Exception;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\FrontController\Command\Command;
use Iquety\Application\Http\HttpFactory;
use Psr\Http\Message\ResponseInterface;

class ErrorCommand extends Command
{
    public function __construct()
    {
    }

    public function execute(Input $input): ResponseInterface
    {
        /** @var HttpFactory $factory */
        $factory = $this->make(HttpFactory::class);

        $response = $factory->createResponse(500);

        return $response->withBody($factory->createStream((string)$input));
    }
}
