<?php

declare(strict_types=1);

namespace Tests\Application\Support\Controllers;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\AppEngine\Input;
use Iquety\Application\AppEngine\Mvc\Controller;
use Psr\Http\Message\ResponseInterface;

class Multi extends Controller
{
    public function __construct()
    {
    }

    /** @SuppressWarnings(PHPMD.ShortVariable) */
    public function create(Input $input, int $id): ResponseInterface
    {
        $message = 'Resposta do controlador para id ' . $id . ' input ' . $input;

        $factory = new DiactorosHttpFactory();

        return $factory->createResponse()->withBody(
            $factory->createStream($message)
        );
    }
}
