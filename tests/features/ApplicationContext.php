<?php

use Behat\Behat\Context\Context;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\HttpFactory\GuzzleHttpFactory;
use Iquety\Application\Adapter\HttpFactory\NyHolmHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\FrontController\Directory;
use Iquety\Application\AppEngine\FrontController\DirectorySet;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
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

    private function makeCustomRequest(HttpFactory $httpFactory): void
    {
        // Em Application, o código (new HttpDependencies())->attachTo($this) 
        // registra uma ServerRequest usando as variáveis globais do servidor

        // O código abaixo irá registrar uma ServerRequest personalizada
        // Quando (new HttpDependencies())->attachTo($this) for executado,
        // identificará que já existe uma ServerRequest e não irá registrá-la
        // mantendo a versão implementada aqui

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

    private function startModules(string $requestUri): void
    {
        if (isset($this->requestParams['engine-fc']) === true) {
            Application::instance()->bootModule(
                new class($requestUri) extends FcBootstrap
                {
                    public function __construct(private string $uri)
                    {}

                    public function bootDirectories(DirectorySet $directories): void
                    {
                        $directories->add(new Directory(
                            'Tests\FcCommands',
                            __DIR__ . '/stubs/FcCommands'
                        ));
                    }

                    public function commandsDirectory(): string
                    {
                        return 'stubs/FcCommands';
                    }
    
                    public function bootDependencies(Application $app): void
                    {}
                }
            );
        }

        if (isset($this->requestParams['engine-mvc']) === true) {
            Application::instance()->bootModule(
                new class($requestUri) extends MvcBootstrap
                {
                    public function __construct(private string $uri)
                    {}

                    public function bootRoutes(Router $router): void
                    {
                        if ($this->uri === '/test/error') {
                            $router->get($this->uri)->usingAction(function(){
                                throw new Exception('Exceção lançada na execução do recurso solicitado');
                            });
                        }
                    }
    
                    public function bootDependencies(Application $app): void
                    {
                    }
                }
            );
        }
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
        $this->requestParams['engine-fc'] = true;

        Application::instance()->bootEngine(new FcEngine());
    }

    /**
     * @Given com mecanismo Mvc
     */
    public function comMecanismoMvc()
    {
        $this->requestParams['engine-mvc'] = true;

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
        $httpFactory = new DiactorosHttpFactory();

        $this->requestParams['httpFactory'] = $httpFactory;

        Application::instance()->bootApplication($this->makeBootstrap(function(Application $app) use($httpFactory) {
            $app->addSingleton(Session::class, MemorySession::class);

            $app->addSingleton(HttpFactory::class, fn() => $httpFactory);
        }));
    }

    /**
     * @Given bootstrap com dependência Session e GuzzleHttpFactory
     */
    public function bootstrapComDependenciaSessionEGuzzleHttpFactory()
    {
        $httpFactory = new GuzzleHttpFactory();

        $this->requestParams['httpFactory'] = $httpFactory;

        Application::instance()->bootApplication($this->makeBootstrap(function(Application $app) use($httpFactory) {
            $app->addSingleton(Session::class, MemorySession::class);

            $app->addSingleton(HttpFactory::class, fn() => $httpFactory);
        }));
    }

    /**
     * @Given bootstrap com dependência Session e NyHolmHttpFactory
     */
    public function bootstrapComDependenciaSessionENyHolmHttpFactory()
    {
        $httpFactory = new NyHolmHttpFactory();

        $this->requestParams['httpFactory'] = $httpFactory;

        Application::instance()->bootApplication($this->makeBootstrap(function(Application $app) use($httpFactory) {
            $app->addSingleton(Session::class, MemorySession::class);

            $app->addSingleton(HttpFactory::class, fn() => $httpFactory);
        }));
    }

    /**
     * @Given o tipo solicitado em DiactorosHttpFactory for :mimeType
     */
    public function oTipoSolicitadoEmDiactorosHttpFactoryFor(string $mimeType)
    {
        $this->requestParams['accept'] = HttpMime::makeBy($mimeType)->value;
        $this->requestParams['httpFactory'] = new DiactorosHttpFactory();
    }

    /**
     * @Given o tipo solicitado em GuzzleHttpFactory for :mimeType
     */
    public function oTipoSolicitadoEmGuzzleHttpFactoryFor(string $mimeType)
    {
        $this->requestParams['accept'] = HttpMime::makeBy($mimeType)->value;
        $this->requestParams['httpFactory'] = new GuzzleHttpFactory();
    }

    /**
     * @Given o tipo solicitado em NyHolmHttpFactory for :mimeType
     */
    public function oTipoSolicitadoEmNyholmhttpfactoryFor(string $mimeType)
    {
        $this->requestParams['accept'] = HttpMime::makeBy($mimeType)->value;
        $this->requestParams['httpFactory'] = new NyHolmHttpFactory();
    }

    /**
     * @Given com rota :uri
     */
    public function comRota(string $uri)
    {
        $this->requestParams['uri'] = $uri;
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
            if (isset($this->requestParams['httpFactory']) === true) {
                $this->makeCustomRequest($this->requestParams['httpFactory']);
            }
    
            if (isset($this->requestParams['uri']) === true) {
                $this->startModules($this->requestParams['uri']);
            }

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
