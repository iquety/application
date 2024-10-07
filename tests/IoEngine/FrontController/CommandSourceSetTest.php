<?php

declare(strict_types=1);

namespace Tests\IoEngine\FrontController;

use InvalidArgumentException;
use Iquety\Application\IoEngine\FrontController\CommandSource;
use Iquety\Application\IoEngine\FrontController\CommandSourceSet;
use Iquety\Application\IoEngine\Module;
use Tests\TestCase;

class CommandSourceSetTest extends TestCase
{
    /** @test */
    public function hasSources(): void
    {
        $source = new CommandSource('Tests\Run\Actions');

        $sourceSet = new CommandSourceSet(Module::class);


        $this->assertFalse($sourceSet->hasSources());

        $sourceSet->add($source);

        $this->assertTrue($sourceSet->hasSources());

        $this->assertEquals(
            [ $source->getIdentity() => $source ],
            $sourceSet->toArray()
        );
    }

    /** @test */
    public function duplicatedSources(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The specified source already exists');

        $sourceSet = new CommandSourceSet(Module::class);

        $this->assertFalse($sourceSet->hasSources());

        $sourceSet->add(new CommandSource('Tests\Run\Actions'));
        $sourceSet->add(new CommandSource('Tests\Run\Actions'));
    }
}
