<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Console;

use Iquety\Application\AppEngine\Action\Makeable;
use Iquety\Application\AppEngine\Action\MethodChecker;
use Iquety\Application\PubSub\Publisher;
use Iquety\Console\Routine;

abstract class Script extends Routine
{
    use Makeable;
    use MethodChecker;
    use Publisher;
}
