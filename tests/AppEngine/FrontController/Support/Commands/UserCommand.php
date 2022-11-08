<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController\Support\Commands;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\AppEngine\FrontController\Command;
use Iquety\Application\AppEngine\Input;
use Psr\Http\Message\ResponseInterface;

class UserCommand extends Command
{
    public function __construct()
    {
    }

    public function execute(Input $input): ResponseInterface
    {
        $message = 'Resposta do comando para id ' . $input;

        $factory = new DiactorosHttpFactory();

        return $factory->createResponse()->withBody(
            $factory->createStream($message)
        );
    }
}
