<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\Mvc\Support\Controllers;

use Iquety\Application\AppEngine\Mvc\Controller;
use Iquety\Application\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class InjectionController extends Controller
{
    public function __construct(private Application $app)
    {
    }

    /** @SuppressWarnings(PHPMD.ShortVariable) */
    public function create(ServerRequestInterface $request, int $id): ResponseInterface
    {
        $message = 'Resposta do controlador para '
            . $request->getUri()->getPath()
             . ' - ID: ' . $id;

        return $this->app->make(ResponseInterface::class)->withBody(
            $this->app->make(StreamInterface::class, $message)
        );
    }
}
