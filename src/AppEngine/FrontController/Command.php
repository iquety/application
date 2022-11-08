<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use Iquety\Application\AppEngine\Makeable;
use Iquety\Application\AppEngine\MethodChecker;

abstract class Command
{
    use Makeable;
    use MethodChecker;
}
