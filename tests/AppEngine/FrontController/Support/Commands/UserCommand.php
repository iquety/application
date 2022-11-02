<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController\Support\Commands;

use Iquety\Application\AppEngine\FrontController\Command;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserCommand extends Command
{
    public function __construct(private ServerRequestInterface $request)
    {
    }

    public function execute(): ResponseInterface
    {
        return (new HttpResponseFactory(Application::instance()))
            ->response('Resposta do comando UserCommand');
    }
}