<?php

declare(strict_types=1);

namespace Tests\Application;

use ArrayObject;
use Iquety\Application\Application;
use Iquety\Application\IoEngine\Console\ConsoleEngine;
use Iquety\Application\IoEngine\Console\ConsoleInput;
use Iquety\Application\IoEngine\Console\ConsoleOutput;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Application\IoEngine\Mvc\MvcModule;
use Iquety\Console\Terminal;
use Iquety\Http\HttpMethod;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationRunnersTest extends TestCase
{
    /** @test */
    public function runCli(): void
    {
        $application = Application::instance();

        $application->bootEngine(new ConsoleEngine());

        $application->bootApplication($this->makeConsoleModuleOne());

        /** @var Module $fakeSecondaryModule */
        $fakeSecondaryModule = $this->createMock(MvcModule::class);
        $application->bootModule($fakeSecondaryModule);

        $output = $application->run(new ConsoleInput(['command', '--arg1', '--arg2']));

        $this->assertInstanceOf(ConsoleOutput::class, $output);
    }

    /** @test */
    public function runCliWithoutConsoleEngine(): void
    {
        $application = Application::instance();

        $application->bootEngine(new MvcEngine());

        $module = $this->makeMvcModuleMinimal(
            HttpMethod::ANY,
            '/',
            ArrayObject::class,
        );

        $application->bootApplication($module);

        $output = $application->run(new ConsoleInput(['command', '--arg1', '--arg2']));

        $this->assertInstanceOf(ConsoleOutput::class, $output);
        $this->assertSame(Terminal::STATUS_ERROR, $output->getStatusCode());
        $this->assertSame(
            'There is no engine capable of interpreting terminal commands',
            $output->getBody()
        );
    }

    /** @test */
    public function runWeb(): void
    {
        $application = Application::instance();

        $application->bootEngine(new MvcEngine());

        $application->bootApplication($this->makeMvcModuleMinimal());

        $output = $application->run($this->makeServerRequest());

        $this->assertInstanceOf(ResponseInterface::class, $output);
    }
}
