<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController\Command;

use Iquety\Application\AppEngine\Input;
use Iquety\Application\Http\HttpFactory;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ErrorCommand extends Command
{
    public function __construct()
    {
    }

    public function execute(Input $input): ResponseInterface
    {
        /** @var HttpFactory $factory */
        $factory = $this->make(HttpFactory::class);

        $response = $factory->createResponse(500);

        $message = $this->makeMessage($this->make(Throwable::class));

        return $response->withBody($factory->createStream($message));
    }

    private function makeMessage(Throwable $exception): string
    {
        return sprintf(
            "Error: %s on file %s[%d]<br>%s",
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
    }
}
