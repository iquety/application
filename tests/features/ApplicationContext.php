<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\HttpFactory\GuzzleHttpFactory;
use Iquety\Application\Adapter\HttpFactory\NyHolmHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ApplicationContext implements Context
{
    private ?Application $app = null;

    private ?ResponseInterface $response = null;

    private ?Throwable $exception = null;

    private function makeBootstrap(Closure $bootCallback): Bootstrap
    {
        return new class($bootCallback) implements Bootstrap {
            public function __construct(private Closure $bootCallback)
            {}

            public function bootDependencies(Application $app): void
            {
                $call = $this->bootCallback;

                $call($app);
            }
        };
    }

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    // DADO
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

    /**
     * @Given uma aplicação instanciada
     */
    public function umaAplicacaoInstanciada()
    {
        $this->app = Application::instance();

        $this->app->reset();
    }

    /**
     * @Given com mecanismo FrontController
     */
    public function comMecanismoFrontController()
    {
        $this->app->bootEngine(new FcEngine());
    }

    /**
     * @Given com mecanismo Mvc
     */
    public function comMecanismoMvc()
    {
        $this->app->bootEngine(new MvcEngine());
    }

    /**
     * @Given sem mecanismo web
     */
    public function semMecanismoWeb()
    {
        // ...
    }

    /**
     * @Given sem bootstrap
     */
    public function semBootstrap()
    {
        // ...
    }

    /**
     * @Given bootstrap com Exceção
     */
    public function bootstrapComExcecao()
    {
        $this->app->bootApplication($this->makeBootstrap(fn() => throw new Exception()));
    }

    /**
     * @Given bootstrap sem dependências
     */
    public function bootstrapSemDependencias()
    {
        $this->app->bootApplication($this->makeBootstrap(fn() => null));
    }

    /**
     * @Given bootstrap com dependência Session inválida
     */
    public function bootstrapComDependenciaSessionInvalida()
    {
        $this->app->bootApplication($this->makeBootstrap(function(Application $app) {
            $app->addSingleton(Session::class, fn() => (object)[]);
        }));
    }

    /**
     * @Given bootstrap com dependência Session
     */
    public function bootstrapComDependenciaSession()
    {
        $this->app->bootApplication($this->makeBootstrap(function(Application $app) {
            $app->addSingleton(Session::class, MemorySession::class);
        }));
    }

    /**
     * @Given bootstrap com dependência Session e HttpFactory inválida
     */
    public function bootstrapComDependenciaSessionEHttpfactoryInvalida()
    {
        $this->app->bootApplication($this->makeBootstrap(function(Application $app) {
            $app->addSingleton(Session::class, MemorySession::class);

            $app->addSingleton(HttpFactory::class, fn() => (object)[]);
        }));
    }

    /**
     * @Given bootstrap com dependência Session e DiactorosHttpFactory
     */
    public function bootstrapComDependenciaSessionEDiactoroshttpfactory()
    {
        $this->app->bootApplication($this->makeBootstrap(function(Application $app) {
            $app->addSingleton(Session::class, MemorySession::class);

            $app->addSingleton(HttpFactory::class, DiactorosHttpFactory::class);
        }));
    }

    /**
     * @Given bootstrap com dependência Session e GuzzleHttpFactory
     */
    public function bootstrapComDependenciaSessionEGuzzlehttpfactory()
    {
        $this->app->bootApplication($this->makeBootstrap(function(Application $app) {
            $app->addSingleton(Session::class, MemorySession::class);

            $app->addSingleton(HttpFactory::class, GuzzleHttpFactory::class);
        }));
    }

    /**
     * @Given bootstrap com dependência Session e NyHolmHttpFactory
     */
    public function bootstrapComDependenciaSessionENyholmhttpfactory()
    {
        $this->app->bootApplication($this->makeBootstrap(function(Application $app) {
            $app->addSingleton(Session::class, MemorySession::class);

            $app->addSingleton(HttpFactory::class, NyHolmHttpFactory::class);
        }));
    }
    
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    // QUANDO
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

    /**
     * @When a aplicação for executada
     */
    public function aAplicacaoForExecutada()
    {
        try {
            $this->response = $this->app->run();
        } catch(Throwable $exception) {
            $this->exception = $exception;
        }
    }

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    // ENTÃO
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

    /**
     * @Then será emitida uma exceção do tipo :expectClassType
     */
    public function seraEmitidaUmaExcecaoDoTipo(string $expectClassType)
    {
        $actualClassType = $this->exception::class;

        if ($actualClassType !== $expectClassType) {
            throw new Exception("Esperada exceção $expectClassType, mas recebida $actualClassType");
        }
    }

    /**
     * @Then a exceção conterá a mensagem :expectedMessage
     */
    public function aExcecaoConteraAMensagem(string $expectedMessage)
    {
        $actualMessage = $this->exception->getMessage();

        if (strpos($actualMessage, $expectedMessage) === false) {
            throw new Exception("Esperada exceção com mensagem $expectedMessage, mas recebida $actualMessage");
        }
    }

    /**
     * @Then o container não possuirá dependência Session
     */
    public function oContainerNaoPossuiraDependenciaSession()
    {
        if ($this->app->container()->has(Session::class) === true) {
            throw new Exception("Não era para Session ter sido declarada");
        }
    }

    /**
     * @Then o container não possuirá dependência HttpFactory
     */
    public function oContainerNaoPossuiraDependenciaHttpfactory()
    {
        if ($this->app->container()->has(HttpFactory::class) === true) {
            throw new Exception("Não era para HttpFactory ter sido declarada");
        }
    }

    /**
     * @Then o container possuirá dependência Session
     */
    public function oContainerPossuiraDependenciaSession()
    {
        if ($this->app->container()->has(Session::class) === false) {
            throw new Exception("Esperada a dependência Session, mas ela não foi declarada");
        }
    }

    /**
     * @Then o container possuirá dependência HttpFactory
     */
    public function oContainerPossuiraDependenciaHttpFactory()
    {
        if ($this->app->container()->has(HttpFactory::class) === false) {
            throw new Exception("Esperada a dependência HttpFactory, mas ela não foi declarada");
        }
    }

    /**
     * @Then o container possuirá dependência Application
     */
    public function oContainerPossuiraDependenciaApplication()
    {
        if ($this->app->container()->has(Application::class) === false) {
            throw new Exception("Esperada a dependência Application, mas ela não foi declarada");
        }
    }

    /**
     * @Then o container possuirá dependência ServerRequestInterface
     */
    public function oContainerPossuiraDependenciaServerRequestInterface()
    {
        if ($this->app->container()->has(ServerRequestInterface::class) === false) {
            throw new Exception("Esperada a dependência ServerRequestInterface, mas ela não foi declarada");
        }
    }    
}
