<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
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

/**
 * Defines application features from the specific context.
 */
class MvcResponse404Context implements Context
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
            new class ($httpFactory) implements Bootstrap {
                public function __construct(private HttpFactory $httpFactory)
                {
                }

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
                    $router->get('/article/:id')->usingAction(function () {
                    });
                    // $router->post('/article/:id');
                }

                public function bootDependencies(Application $app): void
                {
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

        $uri = $this->httpFactory->createUri('/not-exists');

        Application::instance()->addSingleton(
            ServerRequestInterface::class,
            fn() => $serverRequest->withAddedHeader('Accept', $acceptHeader->value)->withUri($uri)
        );
    }

    /**
     * @Given um mecanismo Mvc
     */
    public function umMecanismoMvc()
    {
        $this->makeApplication(new DiactorosHttpFactory());

        Application::instance()->bootEngine(new MvcEngine());
    }

    /**
     * @Given uma rota configurada
     */
    public function umaRotaConfigurada()
    {
        // throw new PendingException();
    }

    /**
     * @When for solicitada uma rota inexistente
     */
    public function forSolicitadaUmaRotaInexistente()
    {
        $this->attachCustomRequest(HttpMime::HTML);

        $this->response = Application::instance()->run();
    }

    /**
     * @Then a resposta será Not Found
     */
    public function aRespostaSeraNotFound()
    {
        $actualStatus = $this->response->getStatusCode();

        if ($actualStatus !== 404) {
            throw new Exception(
                "Esperada resposta com status 404, mas recebido status $actualStatus"
            );
        }
    }

    /**
     * @Then a resposta Not found conterá :expectedFile
     */
    public function aRespostaNotFoundContera(string $expectedFile)
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
     * @Then a resposta Not found será do tipo Html
     */
    public function aRespostaNotFoundSeraDoTipoHtml()
    {
        $actualMimeType   = $this->response->getHeaderLine('Content-type');

        if ($actualMimeType !== HttpMime::HTML->value) {
            throw new Exception(sprintf(
                "Esperada resposta do tipo '%s', mas recebida '%s'",
                HttpMime::HTML->value,
                $actualMimeType
            ));
        }
    }
}
