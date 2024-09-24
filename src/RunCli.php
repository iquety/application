<?php

declare(strict_types=1);

namespace Iquety\Application;

use Behat\Behat\HelperContainer\Exception\ContainerException;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpMime;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Application\Http\Session;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Console\ConsoleInput;
use Iquety\Application\IoEngine\Console\ConsoleOutput;
use Iquety\Application\IoEngine\EngineSet;
use Iquety\Application\IoEngine\IoEngine;
use Iquety\Application\IoEngine\Module;
use Iquety\Injection\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use ReflectionClass;
use RuntimeException;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RunCli
{
    public function __construct(
        private Environment $environment,
        private Container $container,
        private Module $mainModule,
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
        
        $this->engineSet->resolve($input); // o terminal encerra aqui

        // TODO melhorar isso para não precisar devolver uma resposta HTTP 
        // inútil no terminal
        /** @var HttpResponseFactory */
        $responseFactory = $this->container->get(HttpResponseFactory::class);

        return $responseFactory->response('', HttpStatus::OK);

        // return $executor->makeResponseBy($descriptor, $input);
    }
}
