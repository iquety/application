<?php

declare(strict_types=1);

namespace Tests\IoEngine\Mvc\Stubs;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;
use Iquety\Application\Http\HttpFactory;
use Psr\Http\Message\ResponseInterface;

class ResponseController extends Controller
{
    public function __construct()
    {
    }

    /** @SuppressWarnings(PHPMD.ShortVariable) */
    public function execute(Input $input, int $id): ResponseInterface
    {
        /** @var HttpFactory $factory */
        $factory = $this->make(HttpFactory::class);

        $response = $factory->createResponse(200);

        return $response->withBody($factory->createStream(
            'Resposta com base em ResponseInterface para id ' . $id . ' input ' . $input
        ));
    }
}
