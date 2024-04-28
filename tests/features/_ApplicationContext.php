<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpMime;
use Iquety\Application\Http\Session;
use Iquety\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ApplicationContext implements Context
{
    private ?ResponseInterface $response = null;

    private ?HttpFactory $httpFactory = null;

    private string $requestUri = '';

    private ?HttpMime $acceptMimeType = null;

    private function makeApplication(HttpFactory $httpFactory): void
    {
        $this->httpFactory = $httpFactory;

        Application::instance()->reset();

        Application::instance()->bootApplication(
            new class($httpFactory) implements Bootstrap {
                public function __construct(private HttpFactory $httpFactory)
                {}

                public function bootDependencies(Application $app): void
                {
                    // implementação de session
                    $app->addSingleton(Session::class, MemorySession::class); 

                    // implementação de HttpFactory
                    $app->addSingleton(HttpFactory::class, fn() => $this->httpFactory);
                }
            }
        );

        Application::instance()->bootModule(
            new class extends MvcBootstrap
            {
                public function bootRoutes(Router $router): void
                {
                    $router->get('/article/:id')->usingAction(function(){});
                }

                public function bootDependencies(Application $app): void
                {}
            }
        );
    }

    private function attachCustomRequest(): void
    {
        // Em Application, o código (new HttpDependencies())->attachTo($this) 
        // registra uma ServerRequest usando as variáveis globais do servidor

        // O código abaixo irá registrar uma ServerRequest personalizada
        // Quando (new HttpDependencies())->attachTo($this) for executado,
        // identificará que já existe uma ServerRequest e não irá registrá-la
        // mantendo a versão implemntada aqui

        $serverRequest = $this->httpFactory->createRequestFromGlobals();

        if ($this->acceptMimeType !== null) {
            $serverRequest = $serverRequest->withAddedHeader('Accept', $this->acceptMimeType->value);
        }

        if ($this->requestUri !== '') {
            $serverRequest = $serverRequest->withUri($this->httpFactory->createUri($this->requestUri));
        }

        Application::instance()->addSingleton(ServerRequestInterface::class, fn() => $serverRequest);
    }

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    // DADO
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

    /**
     * @Given uma aplicação Diactoros
     */
    public function umaAplicacaoDiactoros()
    {
        $this->makeApplication(new DiactorosHttpFactory());
    }

    /**
     * @Given com arquitetura Mvc
     */
    public function comArquiteturaMvc()
    {
        Application::instance()->bootEngine(new MvcEngine());
    }

    /**
     * @Given com arquitetura FrontController
     */
    public function comArquiteturaFrontController()
    {
        Application::instance()->bootEngine(new FcEngine());
    }

    /**
     * @Given o tipo solicitado for Html
     */
    public function oTipoSolicitadoForHtml()
    {
        $this->acceptMimeType = HttpMime::HTML;
    }

    /**
     * @Given o tipo solicitado for Json
     */
    public function oTipoSolicitadoForJson()
    {
        $this->acceptMimeType = HttpMime::JSON;
    }

    /**
     * @Given o tipo solicitado for Text
     */
    public function oTipoSolicitadoForText()
    {
        $this->acceptMimeType = HttpMime::TEXT;
    }

    /**
     * @Given o tipo solicitado for Xml
     */
    public function oTipoSolicitadoForXml()
    {
        $this->acceptMimeType = HttpMime::XML;
    }

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    // QUANDO
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

    /**
     * @When a solicitação for executada
     */
    public function aSolicitacaoForExecutada()
    {
        $this->attachCustomRequest();

        $this->response = Application::instance()->run();
    }

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    // ENTÃO
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

    /**
     * @Then a resposta terá status :expectedStatus
     */
    public function aRespostaTeraStatus(int $expectedStatus)
    {
        $actualStatus = $this->response->getStatusCode();

        if ($actualStatus !== $expectedStatus) {
            throw new Exception(
                "Esperada resposta com status $expectedStatus, mas recebido status $actualStatus"
            );
        }
    }

    /**
     * @Then a resposta conterá :expectedFile
     */
    public function aRespostaContera(string $expectedFile)
    {
        $actualMessage = (string)$this->response->getBody();

        $expectedMessage = file_get_contents(__DIR__ . "/$expectedFile");
        
        if (strpos($actualMessage, $expectedMessage) === false) {
            throw new Exception(
                "Esperada mensagem contendo '$expectedMessage', mas recebida '$actualMessage'"
            );
        }
    }

    /**
     * @Then a resposta será do tipo :mimeType
     */
    public function aRespostaSeraDoTipo(string $mimeType)
    {
        $expectedMimeType = HttpMime::makeBy($mimeType);
        $actualMimeType   = $this->response->getHeaderLine('Content-type');

        if ($expectedMimeType !== $actualMimeType) {
            throw new Exception(
                "Esperada resposta do tipo '$expectedMimeType', mas recebida '$actualMimeType'"
            );            
        }
    }





    
    /**
     * @Given uma aplicação instanciada
     */
    public function umaAplicacaoInstanciada()
    {
        throw new PendingException();
    }

    /**
     * @When a aplicação for executada
     */
    public function aAplicacaoForExecutada()
    {
        throw new PendingException();
    }

    /**
     * @Then imite uma exceção do tipo :arg1
     */
    public function imiteUmaExcecaoDoTipo($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then a exceção possui a mensagem :arg1
     */
    public function aExcecaoPossuiAMensagem($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given com mecanismo FrontController
     */
    public function comMecanismoFrontcontroller()
    {
        throw new PendingException();
    }

    /**
     * @Given sem Bootstrap
     */
    public function semBootstrap()
    {
        throw new PendingException();
    }

    /**
     * @Given com mecanismo Mvc
     */
    public function comMecanismoMvc()
    {
        throw new PendingException();
    }

    /**
     * @Given Bootstrap com Exceção
     */
    public function bootstrapComExcecao()
    {
        throw new PendingException();
    }

    /**
     * @When Container não terá dependência Session
     */
    public function containerNaoTeraDependenciaSession()
    {
        throw new PendingException();
    }

    /**
     * @Given Bootstrap sem dependências
     */
    public function bootstrapSemDependencias()
    {
        throw new PendingException();
    }

    /**
     * @Then a exceção conterá a mensagem :arg1
     */
    public function aExcecaoConteraAMensagem($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given Bootstrap com dependência Session inválida
     */
    public function bootstrapComDependenciaSessionInvalida()
    {
        throw new PendingException();
    }

    /**
     * @When Container terá dependência Session
     */
    public function containerTeraDependenciaSession()
    {
        throw new PendingException();
    }

    /**
     * @Given Bootstrap com dependência Session
     */
    public function bootstrapComDependenciaSession()
    {
        throw new PendingException();
    }

    /**
     * @When Container não terá dependência HttpFactory
     */
    public function containerNaoTeraDependenciaHttpfactory()
    {
        throw new PendingException();
    }

    /**
     * @Given Bootstrap com dependência Session e HttpFactory inválida
     */
    public function bootstrapComDependenciaSessionEHttpfactoryInvalida()
    {
        throw new PendingException();
    }

    /**
     * @When Container terá dependência HttpFactory
     */
    public function containerTeraDependenciaHttpfactory()
    {
        throw new PendingException();
    }

    /**
     * @Given Bootstrap com dependência Session e HttpFactory
     */
    public function bootstrapComDependenciaSessionEHttpfactory()
    {
        throw new PendingException();
    }

    /**
     * @When Container terá dependência Application
     */
    public function containerTeraDependenciaApplication()
    {
        throw new PendingException();
    }

    /**
     * @When Container terá dependência ServerRequestInterface
     */
    public function containerTeraDependenciaServerrequestinterface()
    {
        throw new PendingException();
    }
}
