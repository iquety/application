<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\CommandHandler;
use Iquety\Application\Application;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Psr\Http\Message\ServerRequestInterface;
use Tests\AppEngine\FrontController\Support\UserBootstrap;
use Tests\AppEngine\FrontController\Support\UserBootstrapAlterDir;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class FrontControllerTestCase extends TestCase
{
    protected function extractNamespace(string $signature, string $add): string
    {
        $signature = explode('\\', $signature);
        array_pop($signature);
        return implode('\\', $signature) . $add;
    }
}
