<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc\Support\Controllers;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\AppEngine\Input;
use Iquety\Application\AppEngine\Mvc\Controller;
use Psr\Http\Message\ResponseInterface;

class UserControllerForMethod extends Controller
{
    public function __construct()
    {
    }

    /** @SuppressWarnings(PHPMD.ShortVariable) */
    public function create(Input $input, int $id): ResponseInterface
    {
        $this->forMethod($this->make('forMethod'));

        $message = 'Resposta do controlador para id ' . $id . ' input ' . $input;

        $factory = new DiactorosHttpFactory();

        return $factory->createResponse()->withBody(
            $factory->createStream($message)
        );
    }
}
