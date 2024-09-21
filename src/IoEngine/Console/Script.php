<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Console;

use Iquety\Application\IoEngine\Action\Makeable;
use Iquety\Application\IoEngine\Action\MethodChecker;
use Iquety\Application\PubSub\Publisher;
use Iquety\Console\Routine;

abstract class Script extends Routine
{
    use Makeable;
    use MethodChecker;
    use Publisher;
}
