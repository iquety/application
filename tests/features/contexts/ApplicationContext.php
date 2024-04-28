<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Iquety\Application\Adapter\HttpFactory\MemoryHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Defines application features from the specific context.
 */
class ApplicationContext implements Context
{
    private ?Application $app = null;

    private ?Throwable $exception = null;

    private ?ResponseInterface $response = null;

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    // DADO
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

    /**
     * @Given uma aplicação instanciada
     */
    public function umaAplicacaoInstanciada()
    {
        $this->app = Application::instance();
        $this->app->reset();

        $this->exception = null;
        $this->response = null;
    }

    /**
     * @Given com mecanismo Mvc
     */
    public function comMecanismoMvc()
    {
        $this->app->bootEngine(new MvcEngine());
    }

    /**
     * @Given com mecanismo FrontController
     */
    public function comMecanismoFrontcontroller()
    {
        $this->app->bootEngine(new FcEngine());
    }

    /**
     * @Given sem Bootstrap
     */
    public function semBootstrap()
    {
        // não declarou o bootstrap
    }

    /**
     * @Given Bootstrap com Exceção
     */
    public function bootstrapComExcecao()
    {
        $this->app->bootApplication(new class implements Bootstrap {
            public function bootDependencies(Application $app): void
            {
                throw new Exception();
            }
        });
    }

    /**
     * @Given Bootstrap sem dependências
     */
    public function bootstrapSemDependencias()
    {
        $this->app->bootApplication(new class implements Bootstrap {
            public function bootDependencies(Application $app): void
            {
                // sem declarar dependências
            }
        });
    }

    /**
     * @Given Bootstrap com dependência Session inválida
     */
    public function bootstrapComDependenciaSessionInvalida()
    {
        $this->app->bootApplication(new class implements Bootstrap {
            public function bootDependencies(Application $app): void
            {
                // não é uma implementação de session
                $app->addSingleton(Session::class, fn() => (object)[]);
            }
        });
    }

    /**
     * @Given Bootstrap com dependência Session
     */
    public function bootstrapComDependenciaSession()
    {
        $this->app->bootApplication(new class implements Bootstrap {
            public function bootDependencies(Application $app): void
            {
                // implementação de session
                $app->addSingleton(Session::class, MemorySession::class);
            }
        });
    }

    /**
     * @Given Bootstrap com dependência Session e HttpFactory inválida
     */
    public function bootstrapComDependenciaSessionEHttpfactoryInvalida()
    {
        $this->app->bootApplication(new class implements Bootstrap {
            public function bootDependencies(Application $app): void
            {
                // implementação de session
                $app->addSingleton(Session::class, MemorySession::class); 

                // não é uma implementação de HttpFactory
                $app->addSingleton(HttpFactory::class, fn() => (object)[]); 
            }
        });
    }

    /**
     * @Given Bootstrap com dependência Session e HttpFactory
     */
    public function bootstrapComDependenciaSessionEHttpfactory()
    {
        $this->app->bootApplication(new class implements Bootstrap {
            public function bootDependencies(Application $app): void
            {
                // implementação de session
                $app->addSingleton(Session::class, MemorySession::class); 

                // implementação de HttpFactory
                $app->addSingleton(HttpFactory::class, MemoryHttpFactory::class);
            }
        });
    }

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    // QUANDO
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

    /**
     * @When a aplicação for executada
     */
    public function aAplicacaoForExecutada()
    {
        $this->exception = null;

        try {
            $this->response = $this->app->run();
        } catch(Throwable $exception) {
            $this->exception = $exception;
        }
    }

    /**
     * @When Container não terá dependência Session
     */
    public function containerNaoTeraDependenciaSession()
    {
        if ($this->app->container()->has(Session::class) === true) {
            throw new Exception("Não era para Session ter sido declarada");
        }
    }

    /**
     * @When Container terá dependência Session
     */
    public function containerTeraDependenciaSession()
    {
        if ($this->app->container()->has(Session::class) === false) {
            throw new Exception("Esperada a dependência Session, mas ela não foi declarada");
        }
    }

    /**
     * @When Container não terá dependência HttpFactory
     */
    public function containerNaoTeraDependenciaHttpfactory()
    {
        if ($this->app->container()->has(HttpFactory::class) === true) {
            throw new Exception("Não era para HttpFactory ter sido declarada");
        }
    }

    /**
     * @When Container terá dependência HttpFactory
     */
    public function containerTeraDependenciaHttpfactory()
    {
        if ($this->app->container()->has(HttpFactory::class) === false) {
            throw new Exception("Esperada a dependência HttpFactory, mas ela não foi declarada");
        }
    }

    /**
     * @When Container terá dependência Application
     */
    public function containerTeraDependenciaApplication()
    {
        if ($this->app->container()->has(Application::class) === false) {
            throw new Exception("Esperada a dependência Application, mas ela não foi declarada");
        }
    }

    /**
     * @When Container terá dependência ServerRequestInterface
     */
    public function containerTeraDependenciaServerrequestinterface()
    {
        if ($this->app->container()->has(ServerRequestInterface::class) === false) {
            throw new Exception("Esperada a dependência ServerRequestInterface, mas ela não foi declarada");
        }
    }

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    // ENTÃO
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

    /**
     * @Then imite uma exceção do tipo :expectType
     */
    public function imiteUmaExcecaoDoTipo($expectType)
    {
        $actualType = $this->exception::class;

        if ($actualType !== $expectType) {
            throw new Exception("Esperada exceção $expectType, mas recebida $actualType");
        }
    }

    /**
     * @Then a exceção possui a mensagem :expectMessage
     */
    public function aExcecaoPossuiAMensagem($expectMessage)
    {
        $actualMessage = $this->exception->getMessage();

        if ($actualMessage !== $expectMessage) {
            throw new Exception("Esperada exceção com mensagem $expectMessage, mas recebida $actualMessage");
        }
    }

    /**
     * @When a resposta será :httpStatus
     */
    public function aRespostaSera(int $expectedStatus)
    {
        $actualStatus = $this->response->getStatusCode();

        if ($actualStatus !== $expectedStatus) {
            throw new Exception(
                "Esperada resposta com status $expectedStatus, mas recebido status $actualStatus"
            );
        }
    }

    /**
     * @When a resposta contém :expectedMessage
     */
    public function aRespostaContem(string $expectedMessage)
    {
        $actualMessage = (string)$this->response->getBody();

        if (strpos($actualMessage, $expectedMessage) === false) {
            throw new Exception(
                "Esperada mensagem contendo '$expectedMessage', mas recebida '$actualMessage'"
            );
        }
    }
}
