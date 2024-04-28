<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Support\Commands;

use Iquety\Application\AppEngine\FrontController\Command;
use Iquety\Application\AppEngine\Input;
use Iquety\Application\Http\HttpFactory;
use Psr\Http\Message\ResponseInterface;

class Multi extends Command
{
    public function __construct()
    {
    }

    public function execute(Input $input): ResponseInterface
    {
        $message = 'Resposta do comando para id ' . $input;

        /** @var HttpFactory */
        $factory = $this->make(HttpFactory::class);

        return $factory->createResponse()->withBody(
            $factory->createStream($message)
        );
    }
}
