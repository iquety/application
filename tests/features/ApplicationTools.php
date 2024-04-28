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
use Iquety\Application\Http\HttpMime;
use Iquety\Application\Http\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ApplicationContext implements Context
{
    private array $requestParams = [];

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

    private function makeCustomRequest(): void
    {
        // Em Application, o código (new HttpDependencies())->attachTo($this) 
        // registra uma ServerRequest usando as variáveis globais do servidor

        // O código abaixo irá registrar uma ServerRequest personalizada
        // Quando (new HttpDependencies())->attachTo($this) for executado,
        // identificará que já existe uma ServerRequest e não irá registrá-la
        // mantendo a versão implementada aqui

        if ($this->requestParams === []) {
            return;
        }

        $httpFactory = $this->requestParams['httpFactory']
            ?? throw new Exception('HttpFactory não fornecido para fabicar a requisição');

        $request = $httpFactory->createRequestFromGlobals();

        if (isset($this->requestParams['accept']) === true) {
            $request = $request->withAddedHeader('Accept', $this->requestParams['accept']);
        }

        if (isset($this->requestParams['uri']) === true) {
            $request = $request->withUri(
                $httpFactory->createUri($this->requestParams['uri'])
            );
        }

        Application::instance()
            ->addSingleton(ServerRequestInterface::class, fn() => $request);
    }

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    // DADO
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

    /**
     * @Given uma aplicação instanciada
     */
    public function umaAplicacaoInstanciada()
    {
        Application::instance()->reset();
    }

    /**
     * @Given com mecanismo FrontController
     */
    public function comMecanismoFrontController()
    {
        Application::instance()->bootEngine(new FcEngine());
    }

    /**
     * @Given com mecanismo Mvc
     */
    public function comMecanismoMvc()
    {
        Application::instance()->bootEngine(new MvcEngine());
    }

    /**
     * @Given sem mecanismo web
     */
    public function semMecanismoWeb()
    {
        // ...
    }

    /**
     * @Given sem rotas ou comandos
     */
    public function semRotasOuComandos()
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
        Application::instance()->bootApplication($this->makeBootstrap(fn() => throw new Exception()));
    }

    /**
     * @Given bootstrap sem dependências
     */
    public function bootstrapSemDependencias()
    {
        Application::instance()->bootApplication($this->makeBootstrap(fn() => null));
    }

    /**
     * @Given bootstrap com dependência Session inválida
     */
    public function bootstrapComDependenciaSessionInvalida()
    {
        Application::instance()->bootApplication($this->makeBootstrap(function(Application $app) {
            $app->addSingleton(Session::class, fn() => (object)[]);
        }));
    }

    /**
     * @Given bootstrap com dependência Session
     */
    public function bootstrapComDependenciaSession()
    {
        Application::instance()->bootApplication($this->makeBootstrap(function(Application $app) {
            $app->addSingleton(Session::class, MemorySession::class);
        }));
    }

    /**
     * @Given bootstrap com dependência Session e HttpFactory inválida
     */
    public function bootstrapComDependenciaSessionEHttpfactoryInvalida()
    {
        Application::instance()->bootApplication($this->makeBootstrap(function(Application $app) {
            $app->addSingleton(Session::class, MemorySession::class);

            $app->addSingleton(HttpFactory::class, fn() => (object)[]);
        }));
    }

    /**
     * @Given bootstrap com dependência Session e DiactorosHttpFactory
     */
    public function bootstrapComDependenciaSessionEDiactorosHttpFactory()
    {
        Application::instance()->bootApplication($this->makeBootstrap(function(Application $app) {
            $app->addSingleton(Session::class, MemorySession::class);

            $app->addSingleton(HttpFactory::class, DiactorosHttpFactory::class);
        }));

        // // só existe após a execução
        // var_dump('9999999', Application::instance()->container()->has(HttpFactory::class));
        // exit;
    }

    /**
     * @Given bootstrap com dependência Session e GuzzleHttpFactory
     */
    public function bootstrapComDependenciaSessionEGuzzlehttpfactory()
    {
        Application::instance()->bootApplication($this->makeBootstrap(function(Application $app) {
            $app->addSingleton(Session::class, MemorySession::class);

            $app->addSingleton(HttpFactory::class, GuzzleHttpFactory::class);
        }));
    }

    /**
     * @Given bootstrap com dependência Session e NyHolmHttpFactory
     */
    public function bootstrapComDependenciaSessionENyholmhttpfactory()
    {
        Application::instance()->bootApplication($this->makeBootstrap(function(Application $app) {
            $app->addSingleton(Session::class, MemorySession::class);

            $app->addSingleton(HttpFactory::class, NyHolmHttpFactory::class);
        }));
    }

    /**
     * @Given o tipo solicitado for Html
     */
    public function oTipoSolicitadoEmForHtml()
    {

        $this->requestParams['accept'] = HttpMime::HTML->value;
    }

    /**
     * @Given o tipo solicitado for Json
     */
    public function oTipoSolicitadoForJson()
    {
        $this->requestParams['accept'] = HttpMime::JSON->value;
    }

    /**
     * @Given o tipo solicitado for Text
     */
    public function oTipoSolicitadoForText()
    {
        $this->requestParams['accept'] = HttpMime::TEXT->value;
    }

    /**
     * @Given o tipo solicitado for Xml
     */
    public function oTipoSolicitadoForXml()
    {
        $this->requestParams['accept'] = HttpMime::XML->value;
    }

    /**
     * @Given o tipo solicitado em DiactorosHttpFactory for Json
     */
    public function oTipoSolicitadoEmDiactorosHttpFactoryForJson()
    {
        $this->requestParams['accept'] = HttpMime::JSON->value;
        $this->requestParams['httpFactory'] = new DiactorosHttpFactory();
    }

    /**
     * @Given o tipo solicitado em GuzzleHttpFactory for Json
     */
    public function oTipoSolicitadoEmGuzzleHttpFactoryForJson()
    {
        $this->requestParams['accept'] = HttpMime::JSON->value;
        $this->requestParams['httpFactory'] = new GuzzleHttpFactory();
    }

    /**
     * @Given o tipo solicitado em NyHolmHttpFactory for Json
     */
    public function oTipoSolicitadoEmNyHolmHttpFactoryForJson()
    {
        $this->requestParams['accept'] = HttpMime::JSON->value;
        $this->requestParams['httpFactory'] = new NyHolmHttpFactory();
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
            $this->makeCustomRequest();

            $this->response = Application::instance()->run();
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
        if (Application::instance()->container()->has(Session::class) === true) {
            throw new Exception("Não era para Session ter sido declarada");
        }
    }

    /**
     * @Then o container não possuirá dependência HttpFactory
     */
    public function oContainerNaoPossuiraDependenciaHttpfactory()
    {
        if (Application::instance()->container()->has(HttpFactory::class) === true) {
            throw new Exception("Não era para HttpFactory ter sido declarada");
        }
    }

    /**
     * @Then o container possuirá dependência Session
     */
    public function oContainerPossuiraDependenciaSession()
    {
        if (Application::instance()->container()->has(Session::class) === false) {
            throw new Exception("Esperada a dependência Session, mas ela não foi declarada");
        }
    }

    /**
     * @Then o container possuirá dependência HttpFactory
     */
    public function oContainerPossuiraDependenciaHttpFactory()
    {
        if (Application::instance()->container()->has(HttpFactory::class) === false) {
            throw new Exception("Esperada a dependência HttpFactory, mas ela não foi declarada");
        }
    }

    /**
     * @Then o container possuirá dependência Application
     */
    public function oContainerPossuiraDependenciaApplication()
    {
        if (Application::instance()->container()->has(Application::class) === false) {
            throw new Exception("Esperada a dependência Application, mas ela não foi declarada");
        }
    }

    /**
     * @Then o container possuirá dependência ServerRequestInterface
     */
    public function oContainerPossuiraDependenciaServerRequestInterface()
    {
        if (Application::instance()->container()->has(ServerRequestInterface::class) === false) {
            throw new Exception("Esperada a dependência ServerRequestInterface, mas ela não foi declarada");
        }
    }    

    /**
     * @Then a resposta terá status :expectedStatus
     */
    public function aRespostaTeraStatus(int $expectedStatus): void
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
    public function aRespostaContera(string $expectedFileOrText): void
    {
        $actualMessage = (string)$this->response->getBody();

        $file = __DIR__ . "/stubs/$expectedFileOrText";

        $expectedMessage = is_file($file) === true
            ? file_get_contents($file)
            : $expectedFileOrText;
        
        if (strpos($actualMessage, $expectedMessage) === false) {
            throw new Exception(
                "Esperada mensagem contendo '$expectedMessage', mas recebida '$actualMessage'"
            );
        }
    }

    /**
     * @Then a resposta será do tipo :mimeType
     */
    public function aRespostaSeraDoTipo(string $mimeType): void
    {
        $expectedMimeType = $mimeType;
        $actualMimeType   = $this->response->getHeaderLine('Content-type');

        if ($expectedMimeType !== $actualMimeType) {
            throw new Exception(
                "Esperada resposta do tipo '$expectedMimeType', mas recebida '$actualMimeType'"
            );            
        }
    }
}
