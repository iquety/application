<?php

declare(strict_types=1);

namespace Iquety\Application\Engine\FrontController;

use Psr\Http\Message\ResponseInterface;

abstract class Command
{
    abstract public function execute(): ResponseInterface;
}
