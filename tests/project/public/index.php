<?php

declare(strict_types=1);

use Freep\Application\Application;
use Freep\Application\Bootstrap;
use Freep\Application\Routing\Router;
use Modules\Admin\AdminBootstrap;
use Modules\Articles\ArticlesBootstrap;

$app = Application::instance();

$app->bootApplication(new class implements Bootstrap {
    public function bootRoutes(Router $router): void
    {
    }
    public function bootDependencies(Application $app): void
    {
    }
});

// o boot configura as rotas e as dependências
// locais dos módulos
$app->bootModule(new AdminBootstrap());
$app->bootModule(new ArticlesBootstrap());

$app->run()->send();
