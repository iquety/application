<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine;

use Iquety\Injection\Container;

interface Module
{
    public function bootDependencies(Container $container): void;

    public function getActionType(): string;

    public function getErrorActionClass(): string;

    public function getMainActionClass(): string;

    public function getNotFoundActionClass(): string;
}
