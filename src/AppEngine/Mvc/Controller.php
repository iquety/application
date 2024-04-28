<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Mvc;

use Iquety\Application\AppEngine\Action\Makeable;
use Iquety\Application\AppEngine\Action\MethodChecker;
use Iquety\Application\AppEngine\Action\Publisher;

abstract class Controller
{
    use Makeable;
    use MethodChecker;
    use Publisher;
}
