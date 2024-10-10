<?php

declare(strict_types=1);

namespace Tests\Support;

use Iquety\Application\IoEngine\Action\ActionDescriptor;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\IoEngine;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\SourceHandler;

trait EngineFactories
{
    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function makeGenericIoEngine(): IoEngine
    {
        global $aaaSourceHandler;

        $aaaSourceHandler = $this->createMock(SourceHandler::class);

        return new class extends IoEngine
        {
            public function boot(Module $module): void
            {
            }

            public function resolve(Input $input): ?ActionDescriptor
            {
                return null;
            }

            public function sourceHandler(): SourceHandler
            {
                global $aaaSourceHandler;

                return $aaaSourceHandler;
            }
        };
    }
}
