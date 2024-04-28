<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Support;

use DateTimeImmutable;
use Iquety\Application\Configuration;

class PostRegisteredPast extends PostRegistered
{
    public function ocurredOn(): DateTimeImmutable
    {
        $this->ocurredOn = new DateTimeImmutable(
            '2022/01/10 10:10:10',
            Configuration::instance()->get('timezone')
        );
        
        return $this->ocurredOn;
    }
}