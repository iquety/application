<?php

declare(strict_types=1);

namespace Iquety\Application;

use Closure;
use Iquety\Application\Http\HttpDependencies;
use InvalidArgumentException;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Injection\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Application
{
    /** @var array<string> */
    private array $appEngineIdentifiers = [];

    /** @var array<Engine> */
    private array $appEngineList = [];

    private Container $container;

    private bool $headersEmission = true;

    private static ?Application $instance = null;    

    private ?Bootstrap $mainBootstrap = null;

    /** @var array<string,Bootstrap> */
    private array $moduleList = [];

    public static function instance(): self
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    private function __construct()
    {
        $this->container = new Container();
    }

    public function addEngine(string $engineIdentifier): void
    {
        $this->appEngineIdentifiers[] = $engineIdentifier;
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

    public function disableHeadersEmission(): void
    {
        $this->headersEmission = false;
    }

    /** @param array<int,mixed> $arguments */
    public function make(...$arguments): mixed
    {
        if ($arguments === []) {
            throw new InvalidArgumentException('Dependency id was not specified');
        }

        /** @var string $identifier */
        $identifier = array_shift($arguments);

        return $this->container()->getWithArguments((string)$identifier, $arguments);
    }

    public function reset(): void
    {
        static::$instance = new self();
    }

    public function run(): ResponseInterface
    {
        if ($this->appEngineIdentifiers === []) {
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

            $this->bootIntoEngines($this->mainBootstrap);

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
        foreach($this->appEngineIdentifiers as $engineIdentifier) {
            $engine = new $engineIdentifier(
                $this->container()
            );

            $engine->boot($bootstrap);

            $this->appEngineList[] = $engine;
        }
    }

    private function executeAppEngine(ServerRequestInterface $request): ResponseInterface
    {
        foreach($this->appEngineList as $engine) {
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
     */
    public function sendResponse(ResponseInterface $response): void
    {
        $this->emitHeaders($response);

        $stream = $response->getBody();
        $stream->rewind();
        echo $stream->getContents();
    }

    protected function emitHeaders(ResponseInterface $response): void
    {
        if ($this->headersEmission === false) {
            return;
        }

        // @codeCoverageIgnoreStart
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }
        // @codeCoverageIgnoreEnd
    }
}
