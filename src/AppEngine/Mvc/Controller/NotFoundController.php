<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Mvc\Controller;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\Http\HttpFactory;
use Psr\Http\Message\ResponseInterface;

class NotFoundController extends Controller
{
    public function execute(): ResponseInterface
    {
        /** @var HttpFactory $factory */
        $factory = $this->make(HttpFactory::class);

        return $factory->createResponse(404);
    }
}
