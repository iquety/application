<?php

declare(strict_types=1);

namespace Iquety\Application;

use Closure;
use Iquety\Application\Http\HttpDependencies;
use InvalidArgumentException;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Injection\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Application
{
    /** @var array<AppEngine> */
    private array $appEngineList = [];

    private Container $container;

    private static ?Application $instance = null;

    private ?Bootstrap $mainBootstrap = null;

    /** @var array<string,Bootstrap> */
    private array $moduleList = [];

    public static function instance(): self
    {
        if (static::$instance === null) { // @phpstan-ignore-line
            static::$instance = new self(); // @phpstan-ignore-line
        }

        return static::$instance; // @phpstan-ignore-line
    }

    private function __construct()
    {
        $this->container = new Container();
    }

    public function bootEngine(AppEngine $engine): void
    {
        $this->appEngineList[] = $engine;
    }

    public function addFactory(string $identifier, Closure|string $factory): void
    {
        $this->container()->registerDependency($identifier, $factory);
    }

    public function addSingleton(string $identifier, Closure|string $factory): void
    {
        $this->container()->registerSingletonDependency($identifier, $factory);
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function bootApplication(Bootstrap $bootstrap): void
    {
        $this->mainBootstrap = $bootstrap;
    }

    public function bootModule(Bootstrap $bootstrap): void
    {
        $this->moduleList[$bootstrap::class] = $bootstrap;
    }

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

    public function reset(): void
    {
        static::$instance = new self(); // @phpstan-ignore-line
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function run(): ResponseInterface
    {
        if ($this->appEngineList === []) {
            throw new RuntimeException('No web engine to handle the request');
        }

        if ($this->mainBootstrap === null) {
            throw new RuntimeException('No bootstrap specified for the application');
        }

        try {
            // registra as dependências especificadas pelo usuário
            $this->mainBootstrap->bootDependencies($this);
        } catch (Throwable $exception) {
            return $this->make(HttpResponseFactory::class)->serverErrorResponse($exception);
        }

        // certifica que as dependências HTTP estejam todas injetadas
        (new HttpDependencies())->attachTo($this);

        try {
            // para o ioc fazer uso da aplicação
            $this->addSingleton(Application::class, fn() => Application::instance());

            if ($this->mainBootstrap !== null) {
                $this->bootIntoEngines($this->mainBootstrap);
            }

            foreach ($this->moduleList as $bootstrap) {
                $this->bootIntoEngines($bootstrap);
            }

            return $this->executeAppEngine(
                $this->make(ServerRequestInterface::class)
            );
        } catch (Throwable $exception) {
            return $this->make(HttpResponseFactory::class)->serverErrorResponse($exception);
        }
    }

    private function bootIntoEngines(Bootstrap $bootstrap): void
    {
        foreach ($this->appEngineList as $engine) {
            $engine->useContainer($this->container());
            $engine->boot($bootstrap);
        }
    }

    private function executeAppEngine(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->appEngineList as $engine) {
            $response = $engine->execute(
                $request,
                $this->moduleList,
                fn($bootstrap) => $bootstrap->bootDependencies($this)
            );

            if ($response !== null) {
                return $response;
            }
        }

        return $this->make(HttpResponseFactory::class)->notFoundResponse();
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
