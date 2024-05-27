<?php

declare(strict_types=1);

namespace Tests\Unit;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\HttpFactory\GuzzleHttpFactory;
use Iquety\Application\Adapter\HttpFactory\NyHolmHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\Source;
use Iquety\Application\AppEngine\FrontController\SourceSet;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\Session;
use Iquety\Injection\Container;

trait ApplicationFc
{
    protected function makeFcBootstrapOne(): FcBootstrap
    {
        return new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('fc-dep-one', fn() => 'one');
            }

            public function bootNamespaces(SourceSet &$sourceSet): void
            {
                $sourceSet->add(new Source(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands'
                ));
            }
        };
    }

    protected function makeFcBootstrapTwo(): FcBootstrap
    {
        return new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('fc-dep-two', fn() => 'two');
            }

            public function bootNamespaces(SourceSet &$sourceSet): void
            {
                $sourceSet->add(new Source(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands\SubDirectory'
                ));
            }
        };
    }

    protected function makeFcBootstrapWithoutDependencies(): FcBootstrap
    {
        return new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                //...
            }

            public function bootNamespaces(SourceSet &$sourceSet): void
            {
                $sourceSet->add(new Source(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands\SubDirectory'
                ));
            }
        };
    }

    protected function makeFcBootstrapInvalidSession(): FcBootstrap
    {
        return new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton(Session::class, fn() => (object)[]);
            }

            public function bootNamespaces(SourceSet &$sourceSet): void
            {
                $sourceSet->add(new Source(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands\SubDirectory'
                ));
            }
        };
    }

    protected function makeFcBootstrapSession(): FcBootstrap
    {
        return new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton(Session::class, MemorySession::class);
            }

            public function bootNamespaces(SourceSet &$sourceSet): void
            {
                $sourceSet->add(new Source(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands\SubDirectory'
                ));
            }
        };
    }

    protected function makeFcBootstrapSessionInvalidHttpFactory(): FcBootstrap
    {
        return new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton(Session::class, MemorySession::class);
                $container->addSingleton(HttpFactory::class, fn() => (object)[]);
            }

            public function bootNamespaces(SourceSet &$sourceSet): void
            {
                $sourceSet->add(new Source(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands\SubDirectory'
                ));
            }
        };
    }

    protected function makeFcBootstrapSessionDiactoros(): FcBootstrap
    {
        return new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton(Session::class, MemorySession::class);
                $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());
            }

            public function bootNamespaces(SourceSet &$sourceSet): void
            {
                $sourceSet->add(new Source(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands\SubDirectory'
                ));
            }
        };
    }

    protected function makeFcBootstrapSessionGuzzle(): FcBootstrap
    {
        return new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton(Session::class, MemorySession::class);
                $container->addSingleton(HttpFactory::class, new GuzzleHttpFactory());
            }

            public function bootNamespaces(SourceSet &$sourceSet): void
            {
                $sourceSet->add(new Source(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands\SubDirectory'
                ));
            }
        };
    }

    protected function makeFcBootstrapSessionNyHolm(): FcBootstrap
    {
        return new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton(Session::class, MemorySession::class);
                $container->addSingleton(HttpFactory::class, new NyHolmHttpFactory());
            }

            public function bootNamespaces(SourceSet &$sourceSet): void
            {
                $sourceSet->add(new Source(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands\SubDirectory'
                ));
            }
        };
    }
}
