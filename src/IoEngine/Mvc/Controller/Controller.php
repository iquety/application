<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Mvc\Controller;

use Iquety\Application\IoEngine\Action\Makeable;
use Iquety\Application\IoEngine\Action\MethodChecker;
use Iquety\Application\IoEngine\Action\Publishable;

abstract class Controller
{
    use Makeable;
    use MethodChecker;
    use Publishable;
}
