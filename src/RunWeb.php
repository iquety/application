<?php

declare(strict_types=1);

namespace Iquety\Application;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\EngineSet;
use Iquety\Application\IoEngine\Module;
use Iquety\Http\HttpFactory;
use Iquety\Http\HttpMime;
use Iquety\Http\Session;
use Iquety\Injection\Container;
use Iquety\Injection\ContainerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use ReflectionClass;
use RuntimeException;
use Throwable;

/** @SuppressWarnings(PHPMD.CouplingBetweenObjects) */
class RunWeb
{
    public function __construct(
        private Environment $environment,
        private Container $container,
        private Module $mainModule,
        private EngineSet $engineSet
    ) {
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $this->consolidateHttpDependencies($request);

        $input = Input::fromRequest($this->container->get(ServerRequestInterface::class));

        $this->container->addSingleton(Application::class, Application::instance());

        $this->container->addSingleton(Input::class, $input);

        try {
            $descriptor = $this->engineSet->resolve($input);
        } catch (Throwable) { // NotFoundException
            $descriptor = $this->engineSet->sourceHandler()->getNotFoundDescriptor();
        }

        $executor = new ActionExecutor($this->container, $this->mainModule);

        return $executor->makeResponseBy($descriptor);
    }

    private function consolidateHttpDependencies(ServerRequestInterface $request): void
    {
        $this->assertSucessfulConstruction(Session::class);

        $this->assertSucessfulConstruction(HttpFactory::class);

        $request = $this->applyDefaultRequestHeaders($request);

        $this->container->addSingleton(
            ServerRequestInterface::class,
            $request
        );

        /** @var HttpFactory $httpFactory */
        $httpFactory = $this->container->get(HttpFactory::class);

        $this->container->addFactory(
            StreamInterface::class,
            fn(string $content = '') => $httpFactory->createStream($content)
        );

        $this->container->addFactory(
            UriInterface::class,
            fn(string $uri = '') => $httpFactory->createUri($uri)
        );

        $this->container->addFactory(
            ResponseInterface::class,
            fn(int $code = 200, string $reasonPhrase = '')
                => $httpFactory->createResponse($code, $reasonPhrase)
        );

        $this->container->addSingleton(
            HttpResponseFactory::class,
            fn() => new HttpResponseFactory($httpFactory, $request)
        );
    }

    private function applyDefaultRequestHeaders(ServerRequestInterface $request): ServerRequestInterface
    {
        if ($request->getHeaderLine('Accept') === "") {
            $request = $request->withAddedHeader('Accept', HttpMime::HTML->value);
        }

        $request = $request->withAddedHeader(
            'Environment',
            $this->environment->value
        );

        return $request;
    }

   /** @param class-string $contract */
    private function assertSucessfulConstruction(string $contract): void
    {
        try {
            $instance = $this->container->get($contract);
        } catch (ContainerException) {
            throw new RuntimeException(sprintf(
                'Please provide an implementation for the %s dependency ' .
                'in the given module in the Application->bootApplication ' .
                'or Application->bootModule method',
                (new ReflectionClass($contract))->getShortName(),
            ));
        }

        if (is_subclass_of($instance, $contract) === false) {
            throw new RuntimeException(sprintf(
                'The implementation provided to the %s dependency in the module ' .
                'provided in the Application->bootApplication method is invalid',
                (new ReflectionClass($contract))->getShortName(),
            ));
        }
    }
}
