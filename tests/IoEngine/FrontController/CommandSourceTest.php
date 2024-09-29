<?php

declare(strict_types=1);

namespace Tests\IoEngine\FrontController;

use Iquety\Application\IoEngine\FrontController\CommandSource;
use Tests\TestCase;

class CommandSourceTest extends TestCase
{
    /** @test */
    public function namespaceValue(): void
    {
        $source = new CommandSource('Tests\Run\Actions');

        $this->assertSame('Tests\Run\Actions', $source->getNamespace());
        $this->assertSame(md5('Tests\Run\Actions'), $source->getIdentity());
    }
}
