<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Console;

use Iquety\Application\IoEngine\Action\Makeable;
use Iquety\Application\IoEngine\Action\MethodChecker;
use Iquety\Application\IoEngine\Action\Publishable;
use Iquety\Console\Routine;

abstract class Script extends Routine
{
    use Makeable;
    use MethodChecker;
    use Publishable;
}
