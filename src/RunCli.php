<?php

declare(strict_types=1);

namespace Iquety\Application;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Console\ConsoleDescriptor;
use Iquety\Application\IoEngine\Console\ConsoleInput;
use Iquety\Application\IoEngine\Console\ConsoleOutput;
use Iquety\Application\IoEngine\EngineSet;
use Iquety\Application\IoEngine\Module;
use Iquety\Console\Terminal;
use Iquety\Injection\Container;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RunCli
{
    public function __construct(
        private Environment $environment, // @phpstan-ignore-line
        private Container $container,
        private Module $mainModule, // @phpstan-ignore-line
        private EngineSet $engineSet
    ) {
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function run(ConsoleInput $consoleInput): ConsoleOutput
    {
        $input = Input::fromConsoleArguments($consoleInput->toArray());

        // para o ioc fazer uso
        $this->container->addSingleton(Application::class, Application::instance());
        $this->container->addSingleton(Input::class, $input);

        /** @var ConsoleDescriptor $descriptor */
        $descriptor = $this->engineSet->resolve($input); // o terminal encerra aqui

        if (! $descriptor instanceof ConsoleDescriptor) {
            return new ConsoleOutput(
                'There is no engine capable of interpreting terminal commands',
                Terminal::STATUS_ERROR
            );
        }

        return new ConsoleOutput($descriptor->output(), $descriptor->status());
    }
}
