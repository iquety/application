<?php

declare(strict_types=1);

namespace Iquety\Application;

use Closure;
use DateTimeZone;
use Iquety\Application\Http\HttpDependencies;
use InvalidArgumentException;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\Bootstrap;
use Iquety\Application\AppEngine\EngineSet;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Injection\Container;
use Iquety\PubSub\Publisher\EventPublisher;
use Iquety\PubSub\Publisher\SimpleEventPublisher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Application
{
    private Container $container;
    
    private EngineSet $engineSet;

    private Environment $environment = Environment::PRODUCTION;

    private static ?Application $instance = null;

    private ?string $mainBootstrap = null;

    private ModuleSet $moduleSet;

    private DateTimeZone $timezone;

    private function __construct()
    {
        $this->container = new Container();

        $this->engineSet = new EngineSet($this->container());

        $this->moduleSet = new ModuleSet();

        $this->useTimezone(new DateTimeZone('America/Sao_Paulo'));
    }

    public static function instance(): self
    {
        if (static::$instance === null) { // @phpstan-ignore-line
            static::$instance = new self(); // @phpstan-ignore-line
        }

        return static::$instance; // @phpstan-ignore-line
    }

    public function bootApplication(Bootstrap $bootstrap): void
    {
        $this->moduleSet->add($bootstrap);

        $this->mainBootstrap = $bootstrap::class;
    }

    public function bootEngine(AppEngine $engine): void
    {
        $this->engineSet->add($engine);
    }

    public function bootModule(Bootstrap $bootstrap): void
    {
        $this->moduleSet->add($bootstrap);
    }

    public function container(): Container
    {
        return $this->container;
    }

    public function runInDevelopmentMode(): void
    {
        $this->environment = Environment::DEVELOPMENT;
    }

    public function runInTestMode(): void
    {
        $this->environment = Environment::TESTING;
    }

    public function runningMode(): Environment
    {
        return $this->environment;
    }

    public function useTimezone(DateTimeZone $timezone): void
    {
        $this->timezone = $timezone;
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

    public function reset(): void
    {
        static::$instance = new self(); // @phpstan-ignore-line
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function run(): ResponseInterface
    {
        if ($this->engineSet->isEmpty() === true) {
            throw new RuntimeException('No web engine to handle the request');
        }

        if ($this->mainBootstrap === null) {
            throw new RuntimeException('No bootstrap specified for the application');
        }

        $mainBootstrap = $this->moduleSet->findByClass($this->mainBootstrap);

        try {
            $mainBootstrap->bootDependencies($this->container());
        } catch (Throwable $exception) {
            throw new RuntimeException('The bootApplication method failed');
        }

        // certifica que as dependências HTTP estejam todas injetadas
        (new HttpDependencies())->attachTo($this);

        try {
            // para o ioc fazer uso da aplicação
            $this->container->addSingleton(Application::class, Application::instance());

            foreach ($this->moduleSet->toArray() as $bootstrap) {
                $this->engineSet->bootAllEngines($this->container(), $bootstrap);
            }

            $response = $this->engineSet->resolveRequest(
                $this->make(ServerRequestInterface::class),
                $this->moduleSet,
                $this
            );
            
            return $response 
                ?? $this->make(HttpResponseFactory::class)->notFoundResponse();

        } catch (Throwable $exception) {
            return $this->make(HttpResponseFactory::class)->serverErrorResponse($exception);
        }
    }

    // private function executeAppEngine(ServerRequestInterface $request): ResponseInterface
    // {
    //     foreach ($this->engineSet->toArray() as $engine) {
    //         $response = $engine->execute($request, $this->moduleSet, $this);

    //         if ($response !== null) {
    //             return $response;
    //         }
    //     }

    //     return $this->make(HttpResponseFactory::class)->notFoundResponse();
    // }

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
