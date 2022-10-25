<?php

declare(strict_types=1);

namespace Freep\Application;

use Closure;
use Freep\Application\Container\Container;
use Freep\Application\Container\InversionOfControl;
use Freep\Application\Http\HttpDependencies;
use Freep\Application\Http\HttpResponseFactory;
use Freep\Application\Routing\Route;
use InvalidArgumentException;
use OutOfBoundsException;
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
    private static ?Application $instance = null;

    private Container $container;

    /** @var array<Engine> */
    private array $engineList = [];

    /** @var array<string,Bootstrap> */
    private array $moduleList = [];

    private bool $headersEmission = true;

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

    public function addEngine(string $identifier): void
    {
        $this->engineList[] = new $identifier($this);
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
        $bootstrap->bootDependencies($this);

        (new HttpDependencies())->attachTo($this);

        // para o ioc fazer uso da aplicação
        $this->addSingleton(Application::class, fn() => Application::instance());

        $this->bootEngines('all', $bootstrap);
    }

    private function bootEngines(string $moduleIdentifier, Bootstrap $bootstrap): void
    {
        foreach($this->engineList as $engine) {
            $engine->boot($moduleIdentifier, $bootstrap);
        }
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

    private function executeEngine(ServerRequestInterface $request): ResponseInterface
    {
        foreach($this->engineList as $engine) {
            $response = $engine->execute($this->moduleList, $request);

            if ($response !== null) {
                return $response;
            }
        }

        return (new HttpResponseFactory($this))->notFoundResponse();
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
        $this->container = new Container();
        $this->engineList = [];
        $this->moduleList = [];
    }

    public function run(): ResponseInterface
    {
        if ($this->engineList === []) {
            throw new RuntimeException('No web engine to handle the request');
        }

        foreach ($this->moduleList as $identifier => $bootstrap) {
            $this->bootEngines($identifier, $bootstrap);
        }

        return $this->executeEngine(
            $this->make(ServerRequestInterface::class)
        );
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
