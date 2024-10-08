<?php

declare(strict_types=1);

namespace Iquety\Application;

use DateTimeZone;
use InvalidArgumentException;
use Iquety\Application\IoEngine\Console\ConsoleInput;
use Iquety\Application\IoEngine\Console\ConsoleOutput;
use Iquety\Application\IoEngine\EngineSet;
use Iquety\Application\IoEngine\IoEngine;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Application\IoEngine\PublisherSet;
use Iquety\Injection\Container;
use Iquety\PubSub\Publisher\EventPublisher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Application
{
    private Container $container;

    private EngineSet $engineSet;

    private Environment $environment = Environment::PRODUCTION;

    private static ?Application $instance = null;

    /**
     * O módulo principal é usado para fabricar as respostas
     * de Erro e NotFound para a execução das ações da aplicação
     */
    private ?Module $mainModule = null;

    private ModuleSet $moduleSet;

    private PublisherSet $publisherSet;

    private DateTimeZone $timeZone;

    private function __construct()
    {
        $this->container = new Container();

        $this->moduleSet = new ModuleSet();

        $this->publisherSet = new PublisherSet();

        $this->engineSet = new EngineSet($this->container);

        $this->useTimezone(new DateTimeZone('America/Sao_Paulo'));

        $this->runIn(Environment::DEVELOPMENT);
    }

    public static function instance(): self
    {
        if (static::$instance === null) { // @phpstan-ignore-line
            static::$instance = new self(); // @phpstan-ignore-line
        }

        return static::$instance; // @phpstan-ignore-line
    }

    public function reset(): void
    {
        static::$instance = new self(); // @phpstan-ignore-line
    }

    public function bootApplication(Module $module): void
    {
        $this->mainModule = $module;

        $this->bootModule($module);
    }

    public function bootModule(Module $module): void
    {
        $this->moduleSet->add($module);
    }

    public function mainModule(): Module
    {
        if ($this->mainModule === null) {
            throw new InvalidArgumentException('Main module was not specified');
        }

        return $this->mainModule;
    }

    public function moduleSet(): ModuleSet
    {
        return $this->moduleSet;
    }

    public function bootEngine(IoEngine $engine): void
    {
        $engine->useModuleSet($this->moduleSet);

        $this->engineSet->add($engine);
    }

    public function engineSet(): EngineSet
    {
        return $this->engineSet;
    }

    public function bootEventPublisher(EventPublisher $publisher): void
    {
        $this->publisherSet->add($publisher);
    }

    public function addSubscriber(string $channel, string $subscriberIdentifier): void
    {
        $this->publisherSet->subscribe($channel, $subscriberIdentifier);
    }

    public function eventPublisherSet(): PublisherSet
    {
        return $this->publisherSet;
    }

    public function runIn(Environment $environment): void
    {
        $this->environment = $environment;
    }

    public function runningMode(): Environment
    {
        return $this->environment;
    }

    /** @see https://www.php.net/manual/en/timezones.php */
    public function useTimeZone(DateTimeZone $timeZone): void
    {
        $this->timeZone = $timeZone;
    }

    public function timeZone(): DateTimeZone
    {
        return $this->timeZone;
    }

    // getters

    public function container(): Container
    {
        return $this->container;
    }

    /** @param mixed ...$arguments */
    public function make(...$arguments): mixed
    {
        if ($arguments === []) {
            throw new InvalidArgumentException('Dependency id was not specified');
        }

        /** @var string $identifier */
        $identifier = array_shift($arguments);

        return $this->container()->getWithArguments(
            (string)$identifier,
            array_values($arguments)
        );
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function run(ServerRequestInterface|ConsoleInput $request): ResponseInterface|ConsoleOutput
    {
        if ($this->engineSet->hasEngines() === false) {
            throw new RuntimeException(
                'No I/O motors were registered in the application'
            );
        }

        if ($this->mainModule === null) {
            throw new RuntimeException(
                'No module was registered in the application'
            );
        }

        try {
            $this->mainModule()->bootDependencies($this->container);
        } catch (Throwable) {
            throw new RuntimeException(sprintf(
                'Method bootApplication failed for module %s',
                $this->mainModule()::class
            ));
        }

        foreach ($this->moduleSet->toArray() as $module) {
            try {
                $module->bootDependencies($this->container);
            } catch (Throwable) {
                throw new RuntimeException(sprintf(
                    'Method bootModule failed for module %s',
                    $module::class
                ));
            }

            /** @throws RuntimeException */
            $this->engineSet()->bootEnginesWith($module);
        }

        if ($request instanceof ConsoleInput) {
            $runner = new RunCli(
                $this->environment,
                $this->container(),
                $this->mainModule(),
                $this->engineSet()
            );

            return $runner->run($request);
        }

        $runner = new RunWeb(
            $this->environment,
            $this->container(),
            $this->mainModule(),
            $this->engineSet()
        );

        return $runner->run($request);
    }

    /**
     * @uses \header
     * @uses \echo
     * @codeCoverageIgnore
     */
    public function sendResponse(ResponseInterface|ConsoleOutput $response): void
    {
        if ($response instanceof ConsoleOutput) {
            echo $response->getBody();

            return;
        }

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        echo (string)$response->getBody();
    }
}
