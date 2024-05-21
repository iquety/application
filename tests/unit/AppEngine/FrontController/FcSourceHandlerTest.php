<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController;

use ArrayObject;
use InvalidArgumentException;
use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\FrontController\Command\ErrorCommand;
use Iquety\Application\AppEngine\FrontController\Command\MainCommand;
use Iquety\Application\AppEngine\FrontController\Command\NotFoundCommand;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcSourceHandler;
use Iquety\Application\AppEngine\FrontController\Source;
use Iquety\Application\AppEngine\FrontController\SourceSet;
use Iquety\Application\AppEngine\Input;
use RuntimeException;
use Tests\Unit\AppEngine\FrontController\Stubs\Commands\OneCommand;
use Tests\Unit\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class FcSourceHandlerTest extends TestCase
{
    /** @test */
    public function commandGetDefaults(): void
    {
        $handler = new FcSourceHandler();

        $this->assertEquals(
            new ActionDescriptor('error', ErrorCommand::class, 'execute'),
            $handler->getErrorDescriptor()
        );

        $this->assertEquals(
            new ActionDescriptor('main', MainCommand::class, 'execute'),
            $handler->getMainDescriptor()
        );

        $this->assertEquals(
            new ActionDescriptor('not-found', NotFoundCommand::class, 'execute'),
            $handler->getNotFoundDescriptor()
        );
    }

    /** @test */
    public function commandSetters(): void
    {
        $handler = new FcSourceHandler();

        $handler->setErrorActionClass(OneCommand::class);
        $handler->setMainActionClass(OneCommand::class);
        $handler->setNotFoundActionClass(OneCommand::class);

        $this->assertEquals(
            new ActionDescriptor('error', OneCommand::class, 'execute'),
            $handler->getErrorDescriptor()
        );

        $this->assertEquals(
            new ActionDescriptor('main', OneCommand::class, 'execute'),
            $handler->getMainDescriptor()
        );

        $this->assertEquals(
            new ActionDescriptor('not-found', OneCommand::class, 'execute'),
            $handler->getNotFoundDescriptor()
        );
    }

    /** @test */
    public function commandSetInvalidError(): void
    {
        $className = ArrayObject::class;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Class $className is not a valid command");

        $handler = new FcSourceHandler();

        $handler->setErrorActionClass($className);
    }

    /** @test */
    public function commandSetInvalidMain(): void
    {
        $className = ArrayObject::class;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Class $className is not a valid command");

        $handler = new FcSourceHandler();

        $handler->setMainActionClass($className);
    }

    /** @test */
    public function commandSetInvalidNotFound(): void
    {
        $className = ArrayObject::class;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Class $className is not a valid command");

        $handler = new FcSourceHandler();

        $handler->setNotFoundActionClass($className);
    }

    /** @test */
    public function descriptorWithoutDirectorySet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No registered sources for getting commands');

        $handler = new FcSourceHandler();

        $handler->getDescriptorTo(Input::fromString('uri/do/comando'));
    }

    /** @test */
    public function descriptorWithEmptyDirectorySet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No registered sources for getting commands');

        $handler = new FcSourceHandler();

        $handler->addSources(new SourceSet(FcBootstrap::class));

        $handler->getDescriptorTo(Input::fromString('uri/do/comando'));
    }

    /** @test */
    public function descriptorNotFound(): void
    {
        $directorySet = new SourceSet(FcBootstrap::class);

        $directorySet->add(new Source(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands'
        ));

        $handler = new FcSourceHandler();

        $handler->addSources($directorySet);

        $this->assertNull(
            $handler->getDescriptorTo(Input::fromString('uri/inexistente'))
        );
    }

    public function mainUriProvider(): array
    {
        $list = [];

        $list['empty'] = [ '' ];

        $list['empty space'] = [ ' ' ];

        $list['bar'] = [ '/' ];
        $list['bar start space'] = [ ' /' ];
        $list['bar finish space'] = [ '/ ' ];
        $list['bar both spaces'] = [ ' / ' ];

        return $list;
    }

    /**
     * @test
     * @dataProvider mainUriProvider
     */
    public function descriptorMain(string $uri): void
    {
        $directorySet = new SourceSet(FcBootstrap::class);

        $directorySet->add(new Source(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
        ));

        $handler = new FcSourceHandler();

        $handler->addSources($directorySet);

        $this->assertEquals(
            new ActionDescriptor('main', MainCommand::class, 'execute'),
            $handler->getDescriptorTo(Input::fromString($uri))
        );
    }

    /** @test */
    public function descriptorCommand(): void
    {
        $directorySet = new SourceSet(FcBootstrap::class);

        $directorySet->add(new Source(
            'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
        ));

        $handler = new FcSourceHandler();

        $handler->addSources($directorySet);

        $this->assertEquals(
            new ActionDescriptor(FcBootstrap::class, OneCommand::class, 'execute'),
            $handler->getDescriptorTo(Input::fromString('one-command/22'))
        );
    }
}
