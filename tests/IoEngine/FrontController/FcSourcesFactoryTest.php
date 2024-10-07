<?php

declare(strict_types=1);

namespace Tests\IoEngine\FrontController;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Action\ActionDescriptor;
use Iquety\Application\IoEngine\FrontController\Command\Command;
use Iquety\Application\IoEngine\FrontController\Command\MainCommand;
use Iquety\Application\IoEngine\FrontController\CommandSource;
use Iquety\Application\IoEngine\FrontController\CommandSourceSet;
use Iquety\Application\IoEngine\FrontController\FcSourceHandler;
use Iquety\Application\IoEngine\Module;
use RuntimeException;
use Tests\IoEngine\FrontController\Stubs\AnyCommand;
use Tests\TestCase;

class FcSourcesFactoryTest extends TestCase
{
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function noSourceHandler(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No registered sources for getting commands');

        $handler = new FcSourceHandler();

        $handler->getDescriptorTo(Input::fromString('/'));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function requestHomeNoSourceHandler(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No registered sources for getting commands');

        $handler = new FcSourceHandler();

        $handler->addSources(new CommandSourceSet(Module::class));

        $handler->getDescriptorTo(Input::fromString('/any'));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function requestHomeWithoutCommand(): void
    {
        $sourceSet = new CommandSourceSet(Module::class);
        $sourceSet->add(new CommandSource('Tests\Run\Actions'));

        $handler = new FcSourceHandler();

        $handler->addSources($sourceSet);

        $descriptor = $handler->getDescriptorTo(Input::fromString('/'));

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        $this->assertSame('main', $descriptor->module());
        $this->assertSame(Command::class, $descriptor->type());
        $this->assertSame(MainCommand::class . '::execute', $descriptor->action());
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function requestNotFoundCommand(): void
    {
        $sourceSet = new CommandSourceSet(Module::class);
        $sourceSet->add(new CommandSource('Tests\Run\Actions'));

        $handler = new FcSourceHandler();

        $handler->addSources($sourceSet);

        $descriptor = $handler->getDescriptorTo(Input::fromString('/not-exists'));

        $this->assertNull($descriptor);
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function requestCommandProcessed(): void
    {
        $input = Input::fromRequest(
            (new DiactorosHttpFactory())->createServerRequest('POST', '/any-command/33')
        );

        $sourceSet = new CommandSourceSet(Module::class);
        $sourceSet->add(new CommandSource('Tests\IoEngine\FrontController\Stubs'));

        $handler = new FcSourceHandler();

        $handler->addSources($sourceSet);

        $descriptor = $handler->getDescriptorTo($input);

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        $this->assertSame(Module::class, $descriptor->module());
        $this->assertSame(Command::class, $descriptor->type());
        $this->assertSame(AnyCommand::class . '::execute', $descriptor->action());
    }
}
