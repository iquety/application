<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\Command\CommandDescriptor;
use Iquety\Application\AppEngine\FrontController\Command\ErrorCommand;
use Iquety\Application\AppEngine\FrontController\Command\MainCommand;
use Iquety\Application\AppEngine\FrontController\Command\NotFoundCommand;
use Iquety\Application\AppEngine\FrontController\SourceHandler;
use Tests\Unit\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class SourceHandlerTest extends TestCase
{
    /** @test */
    public function commandGetDefaults(): void
    {
        $handler = new SourceHandler();

        $this->assertEquals(
            new CommandDescriptor('', ErrorCommand::class, []),
            $handler->getErrorDescriptor()
        );

        $this->assertEquals(
            new CommandDescriptor('', MainCommand::class, []),
            $handler->getMainDescriptor()
        );

        $this->assertEquals(
            new CommandDescriptor('', NotFoundCommand::class, []),
            $handler->getNotFoundDescriptor()
        );
    }

    // /** @test */
    // public function commandSetters(): void
    // {
    //     $handler = new SourceHandler();

    //     $handler->setErrorCommandClass(OneCommand::class);
    //     $handler->setMainCommandClass(OneCommand::class);
    //     $handler->setNotFoundCommandClass(OneCommand::class);

    //     $this->assertEquals(
    //         new CommandDescriptor('', OneCommand::class, []),
    //         $handler->getErrorDescriptor()
    //     );

    //     $this->assertEquals(
    //         new CommandDescriptor('', OneCommand::class, []),
    //         $handler->getMainDescriptor()
    //     );

    //     $this->assertEquals(
    //         new CommandDescriptor('', OneCommand::class, []),
    //         $handler->getNotFoundDescriptor()
    //     );
    // }

    // /** @test */
    // public function commandSetInvalidError(): void
    // {
    //     $className = ArrayObject::class;

    //     $this->expectException(InvalidArgumentException::class);
    //     $this->expectExceptionMessage("Class $className is not a valid command");

    //     $handler = new SourceHandler();

    //     $handler->setErrorCommandClass($className);
    // }

    // /** @test */
    // public function commandSetInvalidMain(): void
    // {
    //     $className = ArrayObject::class;

    //     $this->expectException(InvalidArgumentException::class);
    //     $this->expectExceptionMessage("Class $className is not a valid command");

    //     $handler = new SourceHandler();

    //     $handler->setMainCommandClass($className);
    // }

    // /** @test */
    // public function commandSetInvalidNotFound(): void
    // {
    //     $className = ArrayObject::class;

    //     $this->expectException(InvalidArgumentException::class);
    //     $this->expectExceptionMessage("Class $className is not a valid command");

    //     $handler = new SourceHandler();

    //     $handler->setNotFoundCommandClass($className);
    // }

    // /** @test */
    // public function descriptorWithoutDirectorySet(): void
    // {
    //     $this->expectException(RuntimeException::class);
    //     $this->expectExceptionMessage('No directories registered as command source');

    //     $handler = new SourceHandler();

    //     $handler->getDescriptorTo('uri/do/comando');
    // }

    // /** @test */
    // public function descriptorWithEmptyDirectorySet(): void
    // {
    //     $this->expectException(RuntimeException::class);
    //     $this->expectExceptionMessage('No directories registered as command source');

    //     $handler = new SourceHandler();

    //     $handler->addSources(new DirectorySet(FcBootstrap::class));

    //     $handler->getDescriptorTo('uri/do/comando');
    // }

    // /** @test */
    // public function descriptorNotFound(): void
    // {
    //     $directorySet = new DirectorySet(FcBootstrap::class);

    //     $directorySet->add(new Directory(
    //         'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
    //         __DIR__ . "/Stubs/Commands"
    //     ));

    //     $handler = new SourceHandler();

    //     $handler->addSources($directorySet);

    //     $this->assertEquals(
    //         new CommandDescriptor('', NotFoundCommand::class, []),
    //         $handler->getDescriptorTo('uri/inexistente')
    //     );
    // }

    // public function mainUriProvider(): array
    // {
    //     $list = [];

    //     $list['empty'] = [ '' ];

    //     $list['empty space'] = [ ' ' ];

    //     $list['bar'] = [ '/' ];
    //     $list['bar start space'] = [ ' /' ];
    //     $list['bar finish space'] = [ '/ ' ];
    //     $list['bar both spaces'] = [ ' / ' ];

    //     return $list;
    // }

    // /**
    //  * @test
    //  * @dataProvider mainUriProvider
    //  */
    // public function descriptorMain(string $uri): void
    // {
    //     $directorySet = new DirectorySet(FcBootstrap::class);

    //     $directorySet->add(new Directory(
    //         'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
    //         __DIR__ . "/Stubs/Commands"
    //     ));

    //     $handler = new SourceHandler();

    //     $handler->addSources($directorySet);

    //     $this->assertEquals(
    //         new CommandDescriptor('', MainCommand::class, []),
    //         $handler->getDescriptorTo($uri)
    //     );
    // }

    // /** @test */
    // public function descriptorCommand(): void
    // {
    //     $directorySet = new DirectorySet(FcBootstrap::class);

    //     $directorySet->add(new Directory(
    //         'Tests\Unit\AppEngine\FrontController\Stubs\Commands',
    //         __DIR__ . "/Stubs/Commands"
    //     ));

    //     $handler = new SourceHandler();

    //     $handler->addSources($directorySet);

    //     $this->assertEquals(
    //         new CommandDescriptor(FcBootstrap::class, OneCommand::class, [22]),
    //         $handler->getDescriptorTo('one-command/22')
    //     );
    // }
}
