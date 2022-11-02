<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController\Support\CommandsDir;;

use Iquety\Application\AppEngine\FrontController\Command;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpResponseFactory;
use Psr\Http\Message\ResponseInterface;

class One extends Command
{
    public function execute(): ResponseInterface
    {
        return (new HttpResponseFactory(Application::instance()))
            ->response('test dir only commands');
    }
}