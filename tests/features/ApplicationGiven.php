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

trait ApplicationGiven
{
    /**
     * @Given uma aplicação instanciada
     */
    public function umaAplicacaoInstanciada()
    {
        $this->addStep('instance', Application::instance());
    }

    /**
     * @Given sem mecanismo web
     */
    public function semMecanismoWeb()
    {
        $this->addStep('engine', null);
    }

    /**
     * @Given com mecanismo Mvc
     */
    public function comMecanismoMvc()
    {
        $this->addStep('engine', new MvcEngine());
    }

    /**
     * @Given com mecanismo FrontController
     */
    public function comMecanismoFrontController()
    {
        $this->addStep('engine', new FcEngine());
    }

    /**
     * @Given sem Bootstrap
     */
    public function semBootstrap()
    {
        $this->addStep('bootstrap', null);
    }

    /**
     * @Given Bootstrap com Exceção
     */
    public function bootstrapComExcecao()
    {
        $this->addStep(
            'bootstrap',
            $this->makeBootstrap(fn() => throw new Exception())
        );
    }

    /**
     * @Given com Bootstrap
     */
    public function comBootstrap()
    {
        $this->addStep('bootstrap', $this->makeBootstrap());
    }

    // /**
    //  * @Given Bootstrap com dependência Session inválida
    //  */
    // public function bootstrapComDependenciaSessionInvalida()
    // {
    //     $this->addStep(
    //         'singleton',
    //         $this->makeDependency(Session::class, fn() => (object)[])
    //     );

    //     $this->addStep('bootstrap', $this->makeBootstrap());
    // }

    // /**
    //  * @Given Bootstrap com dependência Session
    //  */
    // public function bootstrapComDependenciaSession()
    // {
    //     $this->addStep(
    //         'singleton',
    //         $this->makeDependency(Session::class, MemorySession::class)
    //     );

    //     $this->addStep('bootstrap', $this->makeBootstrap());
    // }
    
    // /**
    //  * @Given Bootstrap com dependência HttpFactory inválida
    //  */
    // public function bootstrapComDependenciaHttpFactoryInvalida()
    // {
    //     $this->addStep(
    //         'singleton',
    //         $this->makeDependency(HttpFactory::class, fn() => (object)[])
    //     );

    //     $this->addStep('bootstrap', $this->makeBootstrap());
    // }

    // /**
    //  * @Given Bootstrap com dependência DiactorosHttpFactory
    //  */
    // public function bootstrapComDependenciaDiactorosHttpFactory()
    // {
    //     $this->addStep(
    //         'singleton',
    //         $this->makeDependency(HttpFactory::class, fn() => new DiactorosHttpFactory())
    //     );

    //     $this->addStep('bootstrap', $this->makeBootstrap());
    // }

    // /**
    //  * @Given Bootstrap com dependência GuzzleHttpFactory
    //  */
    // public function bootstrapComDependenciaGuzzleHttpFactory()
    // {
    //     $this->addDependencySingleton(HttpFactory::class, fn() => new GuzzleHttpFactory());

    //     $this->addStep('bootstrap', $this->makeBootstrap());
    // }

    // /**
    //  * @Given Bootstrap com dependência NyHolmHttpFactory
    //  */
    // public function bootstrapComDependenciaNyHolmHttpFactory()
    // {
    //     $this->addDependencySingleton(HttpFactory::class, fn() => new NyHolmHttpFactory());

    //     $this->addStep('bootstrap', $this->makeBootstrap());
    // }
}
