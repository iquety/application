<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine;

use Iquety\Application\IoEngine\Action\ActionDescriptor;
use Iquety\Application\IoEngine\Action\Input;

interface SourceHandler
{
    public function getDescriptorTo(Input $input): ?ActionDescriptor;

    public function getErrorDescriptor(): ActionDescriptor;

    public function getMainDescriptor(): ActionDescriptor;

    public function getNotFoundDescriptor(): ActionDescriptor;

    public function setErrorActionClass(string $actionClass): self;

    public function setMainActionClass(string $actionClass): self;

    public function setNotFoundActionClass(string $actionClass): self;
}
