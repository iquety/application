<?php

declare(strict_types=1);

namespace Iquety\Application\Domain;

abstract class AggregateRoot
{
    abstract public static function eventLabel(): string;
}