<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Mvc;

use Iquety\Application\AppEngine\Makeable;
use Iquety\Application\AppEngine\MethodChecker;

abstract class Controller
{
    use Makeable;
    use MethodChecker;
}
