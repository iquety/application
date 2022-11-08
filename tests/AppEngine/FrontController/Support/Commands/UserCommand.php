<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController\Support\Commands;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\AppEngine\FrontController\Command;
use Iquety\Application\AppEngine\Input;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpMethod;
use Psr\Http\Message\ResponseInterface;

class UserCommand extends Command
{
    public function __construct()
    {
    }

    public function execute(Input $input): ResponseInterface
    {
        $this->forMethod($this->make('forMethod'));

        $message = 'Resposta do comando para id ' . $input;

        /** @var HttpFactory */
        $factory = $this->make(HttpFactory::class);

        return $factory->createResponse()->withBody(
            $factory->createStream($message)
        );
    }
}
