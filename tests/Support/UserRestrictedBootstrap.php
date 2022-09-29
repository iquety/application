<?php

declare(strict_types=1);

namespace Tests\Support;

use Freep\Application\Routing\Policy;
use Freep\Application\Routing\Router;

class UserRestrictedBootstrap extends UserBootstrap
{
    public function bootRoutes(Router $router): void
    {
        $router->get('/user/:id')->policyBy(new class implements Policy {
            public function check(): bool
            {
                return false;
            }
        });

        $router->post('/user/:id');
    }
}
