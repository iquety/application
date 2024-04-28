<?php

use Behat\Behat\Context\Context;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\Session;
use Psr\Http\Message\ServerRequestInterface;

class DependenciesContext implements Context
{
    private ?Throwable $exception = null;

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    // DADO
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

    /**
     * @Given uma aplicação instanciada
     */
    public function umaAplicacaoInstanciada()
    {
        Application::instance()->reset();
    }

    /**
     * @Given com mecanismo Mvc
     */
    public function comMecanismoMvc()
    {
        Application::instance()->bootEngine(new MvcEngine());
    }

    /**
     * @Given com mecanismo FrontController
     */
    public function comMecanismoFrontcontroller()
    {
        Application::instance()->bootEngine(new FcEngine());
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
        Application::instance()->bootApplication(new class implements Bootstrap {
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
        Application::instance()->bootApplication(new class implements Bootstrap {
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
        Application::instance()->bootApplication(new class implements Bootstrap {
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
        Application::instance()->bootApplication(new class implements Bootstrap {
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
        Application::instance()->bootApplication(new class implements Bootstrap {
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
        Application::instance()->bootApplication(new class implements Bootstrap {
            public function bootDependencies(Application $app): void
            {
                // implementação de session
                $app->addSingleton(Session::class, MemorySession::class); 

                // implementação de HttpFactory
                $app->addSingleton(HttpFactory::class, DiactorosHttpFactory::class);
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
        try {
            Application::instance()->run();
        } catch(Throwable $exception) {
            $this->exception = $exception;
        }
    }

    /**
     * @When Container não terá dependência Session
     */
    public function containerNaoTeraDependenciaSession()
    {
        if (Application::instance()->container()->has(Session::class) === true) {
            throw new Exception("Não era para Session ter sido declarada");
        }
    }

    /**
     * @When Container terá dependência Session
     */
    public function containerTeraDependenciaSession()
    {
        if (Application::instance()->container()->has(Session::class) === false) {
            throw new Exception("Esperada a dependência Session, mas ela não foi declarada");
        }
    }

    /**
     * @When Container não terá dependência HttpFactory
     */
    public function containerNaoTeraDependenciaHttpfactory()
    {
        if (Application::instance()->container()->has(HttpFactory::class) === true) {
            throw new Exception("Não era para HttpFactory ter sido declarada");
        }
    }

    /**
     * @When Container terá dependência HttpFactory
     */
    public function containerTeraDependenciaHttpfactory()
    {
        if (Application::instance()->container()->has(HttpFactory::class) === false) {
            throw new Exception("Esperada a dependência HttpFactory, mas ela não foi declarada");
        }
    }

    /**
     * @When Container terá dependência Application
     */
    public function containerTeraDependenciaApplication()
    {
        if (Application::instance()->container()->has(Application::class) === false) {
            throw new Exception("Esperada a dependência Application, mas ela não foi declarada");
        }
    }

    /**
     * @When Container terá dependência ServerRequestInterface
     */
    public function containerTeraDependenciaServerrequestinterface()
    {
        if (Application::instance()->container()->has(ServerRequestInterface::class) === false) {
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
     * @Then a exceção conterá a mensagem :expectedMessage
     */
    public function aExcecaoConteraAMensagem($expectedMessage)
    {
        $actualMessage = $this->exception->getMessage();

        if (strpos($actualMessage, $expectedMessage) === false) {
            throw new Exception("Esperada exceção com mensagem $expectedMessage, mas recebida $actualMessage");
        }
    }

    /**
     * @Then a exceção possui a mensagem :expectedMessage
     */
    public function aExcecaoPossuiAMensagem($expectedMessage)
    {
        $actualMessage = $this->exception->getMessage();

        if ($actualMessage !== $expectedMessage) {
            throw new Exception("Esperada exceção com mensagem $expectedMessage, mas recebida $actualMessage");
        }
    }
}
