<?php

use Behat\Behat\Context\Context;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpMime;
use Iquety\Application\Http\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Defines application features from the specific context.
 */
class ResponseErrorContext implements Context
{
    private ?ResponseInterface $response = null;

    private ?HttpFactory $httpFactory = null;

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    // DADO
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    
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
    }

    private function attachCustomRequest(HttpMime $acceptHeader): void
    {
        // Em Application, o código (new HttpDependencies())->attachTo($this) 
        // registra uma ServerRequest usando as variáveis globais do servidor

        // O código abaixo irá registrar uma ServerRequest personalizada
        // Quando (new HttpDependencies())->attachTo($this) for executado,
        // identificará que já existe uma ServerRequest e não irá registrá-la
        // mantendo a versão implemntada aqui

        $serverRequest = $this->httpFactory->createRequestFromGlobals();

        Application::instance()->addSingleton(
            ServerRequestInterface::class,
            fn() => $serverRequest->withAddedHeader('Accept', $acceptHeader->value)
        );
    }

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
     * @Given o tipo solicitado for Html
     */
    public function oTipoSolicitadoForHtml()
    {
        $this->attachCustomRequest(HttpMime::HTML);
    }

    /**
     * @Given o tipo solicitado for Json
     */
    public function oTipoSolicitadoForJson()
    {
        $this->attachCustomRequest(HttpMime::JSON);
    }

    /**
     * @Given o tipo solicitado for Text
     */
    public function oTipoSolicitadoForText()
    {
        $this->attachCustomRequest(HttpMime::TEXT);
    }

    /**
     * @Given o tipo solicitado for Xml
     */
    public function oTipoSolicitadoForXml()
    {
        $this->attachCustomRequest(HttpMime::XML);
    }

    /**
     * @When a solicitação for executada
     */
    public function aSolicitacaoForExecutada()
    {
        $this->response = Application::instance()->run();
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
     * @When a resposta conterá :expectedFile
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
     * @Then a resposta será do tipo Html
     */
    public function aRespostaSeraDoTipoHtml()
    {
        $expectedMimeType = HttpMime::HTML->value;
        $actualMimeType   = $this->response->getHeaderLine('Content-type');

        if ($expectedMimeType !== $actualMimeType) {
            throw new Exception(
                "Esperada resposta do tipo '$expectedMimeType', mas recebida '$actualMimeType'"
            );            
        }
    }

    /**
     * @Then a resposta será do tipo Json
     */
    public function aRespostaSeraDoTipoJson()
    {
        $expectedMimeType = HttpMime::JSON->value;
        $actualMimeType   = $this->response->getHeaderLine('Content-type');

        if ($expectedMimeType !== $actualMimeType) {
            throw new Exception(
                "Esperada resposta do tipo '$expectedMimeType', mas recebida '$actualMimeType'"
            );            
        }
    }

    /**
     * @Then a resposta será do tipo Text
     */
    public function aRespostaSeraDoTipoText()
    {
        $expectedMimeType = HttpMime::TEXT->value;
        $actualMimeType   = $this->response->getHeaderLine('Content-type');

        if ($expectedMimeType !== $actualMimeType) {
            throw new Exception(
                "Esperada resposta do tipo '$expectedMimeType', mas recebida '$actualMimeType'"
            );            
        }
    }

    /**
     * @Then a resposta será do tipo Xml
     */
    public function aRespostaSeraDoTipoXml()
    {
        $expectedMimeType = HttpMime::XML->value;
        $actualMimeType   = $this->response->getHeaderLine('Content-type');

        if ($expectedMimeType !== $actualMimeType) {
            throw new Exception(
                "Esperada resposta do tipo '$expectedMimeType', mas recebida '$actualMimeType'"
            );            
        }
    }
}
