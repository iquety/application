<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\HttpFactory\GuzzleHttpFactory;
use Iquety\Application\Adapter\HttpFactory\NyHolmHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\Console\ConsoleBootstrap;
use Iquety\Application\AppEngine\Console\RoutineSource;
use Iquety\Application\AppEngine\Console\RoutineSourceSet;
use Iquety\Application\AppEngine\FrontController\CommandSource;
use Iquety\Application\AppEngine\FrontController\CommandSourceSet;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\Session;
use Iquety\Injection\Container;

trait ApplicationConsole
{
    protected function makeConsoleBootstrapOne(): ConsoleBootstrap
    {
        return new class extends ConsoleBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('fc-dep-one', fn() => 'one');
            }

            public function bootNamespaces(RoutineSourceSet &$sourceSet): void
            {
                $sourceSet->add(new RoutineSource(
                    'Tests\AppEngine\Console\Stubs'
                ));
            }

            public function getCommandName(): string { return 'command-name'; }

            /** Devolve o diretório real da aplicação que implementa o Console */
            public function getCommandPath(): string { return __DIR__; }
        };
    }

    protected function makeConsoleBootstrapTwo(): ConsoleBootstrap
    {
        return new class extends ConsoleBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('fc-dep-two', fn() => 'two');
            }

            public function bootNamespaces(RoutineSourceSet &$sourceSet): void
            {
                $sourceSet->add(new RoutineSource(
                    'Tests\AppEngine\Console\Stubs\SubDirectory'
                ));
            }

            public function getCommandName(): string { return 'command-name'; }

            /** Devolve o diretório real da aplicação que implementa o Console */
            public function getCommandPath(): string { return __DIR__; }
        };
    }

    // /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    // protected function makeFcBootstrapWithoutDependencies(): FcBootstrap
    // {
    //     return new class extends FcBootstrap {
    //         public function bootDependencies(Container $container): void
    //         {
    //             //...
    //         }

    //         public function bootNamespaces(CommandSourceSet &$sourceSet): void
    //         {
    //             $sourceSet->add(new CommandSource(
    //                 'Tests\AppEngine\FrontController\Stubs\Commands\SubDirectory'
    //             ));
    //         }
    //     };
    // }

    // protected function makeFcBootstrapInvalidSession(): FcBootstrap
    // {
    //     return new class extends FcBootstrap {
    //         public function bootDependencies(Container $container): void
    //         {
    //             $container->addSingleton(Session::class, fn() => (object)[]);
    //         }

    //         public function bootNamespaces(CommandSourceSet &$sourceSet): void
    //         {
    //             $sourceSet->add(new CommandSource(
    //                 'Tests\AppEngine\FrontController\Stubs\Commands\SubDirectory'
    //             ));
    //         }
    //     };
    // }

    // protected function makeFcBootstrapSession(): FcBootstrap
    // {
    //     return new class extends FcBootstrap {
    //         public function bootDependencies(Container $container): void
    //         {
    //             $container->addSingleton(Session::class, MemorySession::class);
    //         }

    //         public function bootNamespaces(CommandSourceSet &$sourceSet): void
    //         {
    //             $sourceSet->add(new CommandSource(
    //                 'Tests\AppEngine\FrontController\Stubs\Commands\SubDirectory'
    //             ));
    //         }
    //     };
    // }

    // protected function makeFcBootstrapSessionInvalidHttpFactory(): FcBootstrap
    // {
    //     return new class extends FcBootstrap {
    //         public function bootDependencies(Container $container): void
    //         {
    //             $container->addSingleton(Session::class, MemorySession::class);
    //             $container->addSingleton(HttpFactory::class, fn() => (object)[]);
    //         }

    //         public function bootNamespaces(CommandSourceSet &$sourceSet): void
    //         {
    //             $sourceSet->add(new CommandSource(
    //                 'Tests\AppEngine\FrontController\Stubs\Commands\SubDirectory'
    //             ));
    //         }
    //     };
    // }

    // protected function makeFcBootstrapSessionDiactoros(): FcBootstrap
    // {
    //     return new class extends FcBootstrap {
    //         public function bootDependencies(Container $container): void
    //         {
    //             $container->addSingleton(Session::class, MemorySession::class);
    //             $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());
    //         }

    //         public function bootNamespaces(CommandSourceSet &$sourceSet): void
    //         {
    //             $sourceSet->add(new CommandSource(
    //                 'Tests\AppEngine\FrontController\Stubs\Commands\SubDirectory'
    //             ));
    //         }
    //     };
    // }

    // protected function makeFcBootstrapSessionGuzzle(): FcBootstrap
    // {
    //     return new class extends FcBootstrap {
    //         public function bootDependencies(Container $container): void
    //         {
    //             $container->addSingleton(Session::class, MemorySession::class);
    //             $container->addSingleton(HttpFactory::class, new GuzzleHttpFactory());
    //         }

    //         public function bootNamespaces(CommandSourceSet &$sourceSet): void
    //         {
    //             $sourceSet->add(new CommandSource(
    //                 'Tests\AppEngine\FrontController\Stubs\Commands\SubDirectory'
    //             ));
    //         }
    //     };
    // }

    // protected function makeFcBootstrapSessionNyHolm(): FcBootstrap
    // {
    //     return new class extends FcBootstrap {
    //         public function bootDependencies(Container $container): void
    //         {
    //             $container->addSingleton(Session::class, MemorySession::class);
    //             $container->addSingleton(HttpFactory::class, new NyHolmHttpFactory());
    //         }

    //         public function bootNamespaces(CommandSourceSet &$sourceSet): void
    //         {
    //             $sourceSet->add(new CommandSource(
    //                 'Tests\AppEngine\FrontController\Stubs\Commands\SubDirectory'
    //             ));
    //         }
    //     };
    // }

    // protected function makeFcBootstrapException(): FcBootstrap
    // {
    //     return new class extends FcBootstrap {
    //         public function bootDependencies(Container $container): void
    //         {
    //             $container->addSingleton(Session::class, MemorySession::class);
    //             $container->addSingleton(HttpFactory::class, new NyHolmHttpFactory());
    //         }

    //         public function bootNamespaces(CommandSourceSet &$sourceSet): void
    //         {
    //             $sourceSet->add(new CommandSource(
    //                 'Tests\AppEngine\FrontController\Stubs\Commands\SubDirectory'
    //             ));
    //         }

    //         public function getErrorActionClass(): string
    //         {
    //             throw new Exception('Proposital exception');
    //         }
    //     };
    // }
}
