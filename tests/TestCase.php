<?php

declare(strict_types=1);

namespace Tests;

use Iquety\Application\Application;
use Iquety\Injection\Container;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use Tests\Support\EngineFactories;
use Tests\Support\HttpFactories;
use Tests\Support\ModuleFactories;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class TestCase extends FrameworkTestCase
{
    use HttpFactories;
    use EngineFactories;
    use ModuleFactories;

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function tearDown(): void
    {
        Application::instance()->reset();
    }

    /**
     * O container precisa ser obtido da instância única da aplicação
     * pois as ações (Controller, Command e Script) usam dessa forma
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function makeContainer(): Container
    {
        return Application::instance()->container();
    }
}
