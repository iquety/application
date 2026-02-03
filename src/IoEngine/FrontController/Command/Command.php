<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\FrontController\Command;

use Iquety\Application\IoEngine\Action\Makeable;
use Iquety\Application\IoEngine\Action\MethodChecker;
use Iquety\Application\IoEngine\Action\Publishable;
use Iquety\Application\IoEngine\Action\Validable;

abstract class Command
{
    use Makeable;
    use MethodChecker;
    use Publishable;
    use Validable;
}
