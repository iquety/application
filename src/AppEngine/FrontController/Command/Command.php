<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController\Command;

use Iquety\Application\AppEngine\Action\Makeable;
use Iquety\Application\AppEngine\Action\MethodChecker;

abstract class Command
{
    use Makeable;
    use MethodChecker;
    // use Publisher;
}
