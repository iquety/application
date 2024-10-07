<?php

declare(strict_types=1);

namespace Tests\IoEngine\Console;

use Iquety\Application\IoEngine\Console\ConsoleDescriptor;
use Iquety\Application\IoEngine\Console\ConsoleModule;
use Iquety\Application\IoEngine\Console\Script;
use Tests\TestCase;

class ConsoleDescriptorTest extends TestCase
{
    /** @test */
    public function getters(): void
    {
        $descriptor = new ConsoleDescriptor(
            Script::class,
            ConsoleModule::class,
            '',
            ''
        );

        $this->assertSame(Script::class, $descriptor->type());
        $this->assertSame(ConsoleModule::class, $descriptor->module());
        $this->assertSame('::', $descriptor->action());
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function factory(): void
    {
        $descriptor = ConsoleDescriptor::factory(ConsoleModule::class, 'saida de terminal', -1);

        $this->assertSame(Script::class, $descriptor->type());
        $this->assertSame(ConsoleModule::class, $descriptor->module());
        $this->assertSame('::', $descriptor->action());

        $this->assertSame('saida de terminal', $descriptor->output());
        $this->assertSame(-1, $descriptor->status());
    }
}
