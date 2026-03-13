<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\FrontController\Command;

use Throwable;

class ErrorCommand extends Command
{
    public function execute(Throwable $exception): string
    {
        return $exception->getMessage();
    }
}
