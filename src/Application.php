<?php

declare(strict_types=1);

namespace Iquety\Application;

use DateTimeZone;
use InvalidArgumentException;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\Bootstrap;
use Iquety\Application\AppEngine\EngineSet;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Application\Http\HttpDependencies;
use Iquety\Injection\Container;
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

    private ?Bootstrap $mainBootstrap = null;

    private ModuleSet $moduleSet;

    private DateTimeZone $timezone;

    private function __construct()
    {
        $this->container = new Container();

        $this->moduleSet = new ModuleSet();

        $this->engineSet = new EngineSet($this->container);

        $this->useTimezone(new DateTimeZone('America/Sao_Paulo'));
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

    public function bootApplication(Bootstrap $bootstrap): void
    {
        $this->moduleSet->add($bootstrap);

        $this->mainBootstrap = $bootstrap;
    }

    public function bootEngine(AppEngine $engine): void
    {
        $engine->useModuleSet($this->moduleSet);

        $this->engineSet->add($engine);
    }

    public function bootModule(Bootstrap $bootstrap): void
    {
        $this->moduleSet->add($bootstrap);
    }

    // setters

    public function runIn(Environment $environment): void
    {
        $this->environment = $environment;
    }

    public function runningMode(): Environment
    {
        return $this->environment;
    }

    /** @see https://www.php.net/manual/en/timezones.php */
    public function useTimezone(DateTimeZone $timezone): void
    {
        $this->timezone = $timezone;
    }

    // getters

    public function container(): Container
    {
        return $this->container;
    }

    public function engineSet(): EngineSet
    {
        return $this->engineSet;
    }

    public function moduleSet(): ModuleSet
    {
        return $this->moduleSet;
    }

    public function timezone(): DateTimeZone
    {
        return $this->timezone;
    }

    // public function addSubscriber(string $channel, string $subscriberIdentifier): void
    // {
    //     $this->eventPublisher()->subscribe($channel, $subscriberIdentifier);
    // }

    // public function eventPublisher(): EventPublisher
    // {
    //     return SimpleEventPublisher::instance();
    // }

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
    public function run(): ResponseInterface
    {
        if ($this->engineSet->hasEngines() === false) {
            throw new RuntimeException('No engine to handle the request');
        }

        if ($this->mainBootstrap === null) {
            throw new RuntimeException('No bootstrap specified for the application');
        }

        try {
            $this->mainBootstrap->bootDependencies($this->container);
        } catch (Throwable) {
            throw new RuntimeException('The bootApplication method failed');
        }

        // certifica que as dependências HTTP estejam todas injetadas
        (new HttpDependencies($this->runningMode()))->attachTo($this->container());

        try {
            foreach ($this->moduleSet->toArray() as $bootstrap) {
                $this->engineSet->bootEnginesWith($bootstrap);
            }
        } catch (Throwable) {
            throw new RuntimeException('The bootModule method failed');
        }

        $input = Input::fromRequest($this->make(ServerRequestInterface::class));

        // para o ioc fazer uso
        $this->container->addSingleton(Application::class, Application::instance());
        $this->container->addSingleton(Input::class, $input);

        $executor = new ActionExecutor($this->container(), $this->mainBootstrap);

        return $executor->makeResponseBy(
            $this->engineSet->resolve($input),
            $input
        );
    }

    /**
     * @uses \header
     * @uses \echo
     * @codeCoverageIgnore
     */
    public function sendResponse(ResponseInterface $response): void
    {
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        echo (string)$response->getBody();
    }
}
