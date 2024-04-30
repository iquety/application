<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController\Stubs\Commands;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\FrontController\Command\Command;
use Iquety\Application\Http\HttpFactory;
use Psr\Http\Message\ResponseInterface;

class OneCommand extends Command
{
    public function __construct()
    {
    }

    public function execute(Input $input): ResponseInterface
    {
        /** @var ResponseInterface */
        return $this->make(HttpFactory::class)->createResponse();
    }
}
