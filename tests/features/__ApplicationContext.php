<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\HttpFactory\GuzzleHttpFactory;
use Iquety\Application\Adapter\HttpFactory\NyHolmHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\AppEngine;
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
    // use ApplicationGiven;

    private array $stepList = [];

    // private array $dependencyList = [];

    // private ?HttpFactory $httpFactory = null;
    
    // private ?string $requestUri = null;
    
    // private ?HttpMime $acceptMimeType = null;

    private ?ResponseInterface $response = null;

    private ?Throwable $exception = null;

    private function addStep(string $name, mixed $asset): void
    {
        $this->stepList[] = (object)[
            'name'  => $name,
            'asset' => $asset
        ];
    }

    private function getStep(string $name): mixed
    {
        $currentStep = null;

        foreach($this->stepList as $index => $step) {
            $currentStep = $step->asset;

            if ($step->name === $name) {
                unset($this->stepList[$index]);
            }
        }

        return $currentStep;
    }

    private function makeDependency(string $signature, mixed $factory): object
    {
        return (object)[
            'signature' => $signature,
            'factory'   => $factory
        ];
    }

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // QUANDO
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    /**
     * @When a aplicação for executada
     */
    public function aAplicacaoForExecutada()
    {
        /** @var Application $asset */
        $application = $this->getStep('instance');

        while($engine = $this->getStep('engine')) {
            var_dump($engine);
        }

        exit;

        foreach($this->stepList as $step) {
            $name = $step->name;
            $asset = $step->asset;

            if ($name === 'instance') {
                
                $application = $asset;
                $application->reset();
            }

            if ($name === 'engine' && $asset !== null) {
                /** @var AppEngine $asset */
                $application->bootEngine($asset);
            }

            if ($name === 'bootstrap' && $asset !== null) {
                /** @var Bootstrap $asset */
                $application->bootApplication($asset);
            }

            var_dump($name);
        }

        exit;
        $this->makeApplication();

        // $this->attachCustomRequest();

        try {
            $this->response = $application->run();
        } catch(Throwable $exception) {
            $this->exception = $exception;
        }
    }
    
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // ENTÃO
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

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
     * @Then o Container não possuirá dependência Session
     */
    public function oContainerNaoPossuiraDependenciaSession()
    {
        if (Application::instance()->container()->has(Session::class) === true) {
            throw new Exception("Não era para Session ter sido declarada");
        }
    }

    /**
     * @Then o Container possuirá dependência Session
     */
    public function oContainerPossuiraDependenciaSession()
    {
        if (Application::instance()->container()->has(Session::class) === false) {
            throw new Exception("Esperada a dependência Session, mas ela não foi declarada");
        }
    }

    /**
     * @Then o Container não possuirá dependência HttpFactory
     */
    public function oContainerNaoPossuiraDependenciaHttpFactory()
    {
        if (Application::instance()->container()->has(HttpFactory::class) === true) {
            throw new Exception("Não era para HttpFactory ter sido declarada");
        }
    }

    /**
     * @Then o Container possuirá dependência HttpFactory
     */
    public function oContainerPossuiraDependenciaHttpFactory()
    {
        if (Application::instance()->container()->has(HttpFactory::class) === false) {
            throw new Exception("Esperada a dependência HttpFactory, mas ela não foi declarada");
        }
    }

    /**
     * @When o Container possuirá dependência Application
     */
    public function oContainerPossuiraDependenciaApplication()
    {
        if (Application::instance()->container()->has(Application::class) === false) {
            throw new Exception("Esperada a dependência Application, mas ela não foi declarada");
        }
    }

    /**
     * @When o Container possuirá dependência ServerRequestInterface
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
    public function aRespostaContera($expectedFile): void
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
    public function aRespostaSeraDoTipo(string $mimeType): void
    {
        $expectedMimeType = HttpMime::makeBy($mimeType);
        $actualMimeType   = $this->response->getHeaderLine('Content-type');

        if ($expectedMimeType !== $actualMimeType) {
            throw new Exception(
                "Esperada resposta do tipo '$expectedMimeType', mas recebida '$actualMimeType'"
            );            
        }
    }
    
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // TOOLS
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    

    private function makeApplication(): void
    {
        // $this->bootstrap = $this->makeBootstrap();

        if ($this->bootstrap !== null) {
            // echo "Bootstrap" . $this->bootstrap::class . "\n";
            Application::instance()->bootApplication($this->bootstrap);
        }
        
        foreach($this->engineList as $engine) {
            // echo $engine::class . "\n";
            Application::instance()->bootEngine($engine);
        }
        
        // Application::instance()->bootApplication(
        //     new class($this->dependencyList) implements Bootstrap {
        //         public function __construct(private array $dependencyList)
        //         {}

        //         public function bootDependencies(Application $app): void
        //         {
        //             foreach($this->dependencyList as $dep) {
        //                 $type      = $dep['type'];
        //                 $signature = $dep['signature'];
        //                 $factory   = $dep['factory'];

        //                 $type === 'singleton'
        //                     ? $app->addSingleton($signature, $factory)
        //                     : $app->addFactory($signature, $factory);
        //             }
                    
        //             // $app->addSingleton(HttpFactory::class, fn() => $this->httpFactory);
        //         }
        //     }
        // );

        // Application::instance()->bootModule(
        //     new class extends MvcBootstrap
        //     {
        //         public function bootRoutes(Router $router): void
        //         {
        //             $router->get('/article/:id')->usingAction(function(){});
        //         }

        //         public function bootDependencies(Application $app): void
        //         {}
        //     }
        // );
    }

    private function makeBootstrap(?Closure $bootCallback = null): Bootstrap
    {
        return new class($bootCallback) implements Bootstrap {
            public function __construct(private ?Closure $callback)
            {}

            public function bootDependencies(Application $app): void
            {
                if ($this->callback !== null) {
                    $call = $this->callback;
                    $call($app);
                }

                // foreach($this->dependencyList as $dep) {
                //     $type      = $dep['type'];
                //     $signature = $dep['signature'];
                //     $factory   = $dep['factory'];
                    
                //     if ($type === 'singleton') {
                //         $app->addSingleton($signature, $factory);

                //         continue;
                //     }

                //     $app->addFactory($signature, $factory);
                // }
            }
        };
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

    // // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    // // DADO
    // // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

    // /**
    //  * @Given uma aplicação Diactoros
    //  */
    // public function umaAplicacaoDiactoros()
    // {
    //     $this->makeApplication(new DiactorosHttpFactory());
    // }

    // /**
    //  * @Given com arquitetura Mvc
    //  */
    // public function comArquiteturaMvc()
    // {
    //     Application::instance()->bootEngine(new MvcEngine());
    // }

    // /**
    //  * @Given com arquitetura FrontController
    //  */
    // public function comArquiteturaFrontController()
    // {
    //     Application::instance()->bootEngine(new FcEngine());
    // }

    // /**
    //  * @Given o tipo solicitado for Html
    //  */
    // public function oTipoSolicitadoForHtml()
    // {
    //     $this->acceptMimeType = HttpMime::HTML;
    // }

    // /**
    //  * @Given o tipo solicitado for Json
    //  */
    // public function oTipoSolicitadoForJson()
    // {
    //     $this->acceptMimeType = HttpMime::JSON;
    // }

    // /**
    //  * @Given o tipo solicitado for Text
    //  */
    // public function oTipoSolicitadoForText()
    // {
    //     $this->acceptMimeType = HttpMime::TEXT;
    // }

    // /**
    //  * @Given o tipo solicitado for Xml
    //  */
    // public function oTipoSolicitadoForXml()
    // {
    //     $this->acceptMimeType = HttpMime::XML;
    // }

    // // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    // // QUANDO
    // // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

    // /**
    //  * @When a solicitação for executada
    //  */
    // public function aSolicitacaoForExecutada()
    // {
    //     $this->attachCustomRequest();

    //     $this->response = Application::instance()->run();
    // }

    // // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    // // ENTÃO
    // // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

    // /**
    //  * @Then a resposta terá status :expectedStatus
    //  */
    // public function aRespostaTeraStatus(int $expectedStatus)
    // {
    //     $actualStatus = $this->response->getStatusCode();

    //     if ($actualStatus !== $expectedStatus) {
    //         throw new Exception(
    //             "Esperada resposta com status $expectedStatus, mas recebido status $actualStatus"
    //         );
    //     }
    // }

    // /**
    //  * @Then a resposta conterá :expectedFile
    //  */
    // public function aRespostaContera(string $expectedFile)
    // {
    //     $actualMessage = (string)$this->response->getBody();

    //     $expectedMessage = file_get_contents(__DIR__ . "/$expectedFile");
        
    //     if (strpos($actualMessage, $expectedMessage) === false) {
    //         throw new Exception(
    //             "Esperada mensagem contendo '$expectedMessage', mas recebida '$actualMessage'"
    //         );
    //     }
    // }

    // /**
    //  * @Then a resposta será do tipo :mimeType
    //  */
    // public function aRespostaSeraDoTipo(string $mimeType)
    // {
    //     $expectedMimeType = HttpMime::makeBy($mimeType);
    //     $actualMimeType   = $this->response->getHeaderLine('Content-type');

    //     if ($expectedMimeType !== $actualMimeType) {
    //         throw new Exception(
    //             "Esperada resposta do tipo '$expectedMimeType', mas recebida '$actualMimeType'"
    //         );            
    //     }
    // }  

    

    

    

    

    

    
}
