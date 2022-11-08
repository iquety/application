<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController\Support\CommandsDir;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\AppEngine\FrontController\Command;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpResponseFactory;
use Psr\Http\Message\ResponseInterface;

class One extends Command
{
    public function __construct(private int $id)
    {
    }

    public function execute(): ResponseInterface
    {
        $message = 'Resposta do comando para id ' . $this->id;

        $factory = new DiactorosHttpFactory();

        return $factory->createResponse()->withBody(
            $factory->createStream($message)
        );
    }
}