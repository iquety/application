<?php

declare(strict_types=1);

namespace Modules\Admin\Commands;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\FrontController\Command;

class RegisterUser extends Command
{
    public function execute(Input $input): void
    {
        $this->publish(User::eventLabel(), );
    }
}

