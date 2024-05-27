<?php

declare(strict_types=1);

namespace Modules\Admin\Domain;

use Iquety\Application\Domain\AggregateRoot;

class User extends AggregateRoot
{
    public static function eventLabel(): string
    {
        return 'user';
    }
}
