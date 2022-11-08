<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc\Support\Controllers;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\AppEngine\Mvc\Controller;
use Psr\Http\Message\ResponseInterface;

class UserController extends Controller
{
    public function __construct()
    {
    }

    /** @SuppressWarnings(PHPMD.ShortVariable) */
    public function create(int $id): ResponseInterface
    {
        $message = 'Resposta do controlador para id ' . $id;

        $factory = new DiactorosHttpFactory();

        return $factory->createResponse()->withBody(
            $factory->createStream($message)
        );
    }
}
