<?php

declare(strict_types=1);

namespace Tests\Run\Actions;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\FrontController\Command\Command;

class TestFlashErrorCommand extends Command
{
    public function execute(Input $input): string
    {
        $input->assert('name')->equalTo('Ricardo Pereira');
        $input->assert('email')->isEmail();

        $input->validOrRedirect('/destination');

        return '';
    }
}
