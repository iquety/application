<?php

declare(strict_types=1);

namespace Tests\Routing;

use Iquety\Application\Routing\Policy;
use Iquety\Application\Routing\Route;
use Tests\TestCase;

class RouteTest extends TestCase
{
    /** @test */
    public function settersGetters(): void
    {
        $policy = new class implements Policy {
            public function check(): bool
            {
                return true;
            }
        };

        $route = (new Route())
            ->forModule('UserBootstrap')
            ->policyBy($policy)
            ->usingPattern('edit/:id')
            ->usingMethod(Route::ANY)
            ->usingAction('UserController');

        $this->assertSame('UserBootstrap', $route->module());
        $this->assertSame($policy, $route->policy());
        $this->assertSame('edit/:id', $route->pattern());
        $this->assertSame(Route::ANY, $route->method());
        $this->assertSame('UserController', $route->action());
        $this->assertSame([], $route->params());
    }

    /** @return array<int, array> */
    public function routesProvider(): array
    {
        $data = [];

        foreach ([ "/:/", "/:_", "_:/", "_:_" ] as $inputs) {
            $insert = explode(":", $inputs);
            $start  = $insert[0] === "_" ? "" : $insert[0];
            $end    = $insert[1] === "_" ? "" : $insert[1];

            // sem parametros
            $data[] = [
                $start . "edit/now" . $end, // pattern
                "edit/now",                 // path
                []                          // result
            ];
            $data[] = [
                "edit/now",
                $start . "edit/now" . $end,
                []
            ];

            // um parametro (:id)
            $data[] = [
                $start . "edit/:id" . $end,
                "edit/now",
                [ "id" => "now" ]
            ];
            $data[] = [
                "edit/:id",
                $start . "edit/now" . $end,
                [ "id" => "now" ]
            ];

            $data[] = [
                $start . ":id/edit" . $end,
                "now/edit",
                [ "id" => "now" ]
            ];
            $data[] = [
                ":id/edit",
                $start . "now/edit" . $end,
                [ "id" => "now" ]
            ];

            // dois parametros (:id, :name)
            $data[] = [
                $start . ":id/:name" . $end,
                "5/ricardo",
                [ "id" => "5", "name" => "ricardo" ]
            ];
            $data[] = [
                $start . "edit/:id/:name" . $end,
                "edit/5/ricardo",
                [ "id" => "5", "name" => "ricardo" ]
            ];
            $data[] = [
                "edit/:id/:name",
                $start . "edit/5/ricardo" . $end,
                [ "id" => "5", "name" => "ricardo" ]
            ];

            $data[] = [
                $start . ":id/edit/:name" . $end,
                "5/edit/ricardo",
                [ "id" => "5", "name" => "ricardo" ]
            ];
            $data[] = [
                ":id/edit/:name",
                $start . "5/edit/ricardo" . $end,
                [ "id" => "5", "name" => "ricardo" ]
            ];
            $data[] = [
                $start . ":id/:name/edit" . $end,
                "5/ricardo/edit",
                [ "id" => "5", "name" => "ricardo" ]
            ];
            $data[] = [
                ":id/:name/edit",
                $start . "5/ricardo/edit" . $end,
                [ "id" => "5", "name" => "ricardo" ]
            ];
        }

        return $data;
    }

    /**
     * @test
     * @dataProvider routesProvider
     * @param array<string, string> $params
    */
    public function matchRoute(string $pattern, string $path, array $params): void
    {
        $route = new Route();
        $route->usingPattern($pattern);
        $route->usingMethod(Route::GET);
        $route->usingAction('CtrlTeste');

        $this->assertTrue($route->matchTo(Route::GET, $path));
        $this->assertEquals($params, $route->params());
        $this->assertEquals(Route::GET, $route->method());
        $this->assertEquals('CtrlTeste', $route->action());
        $this->assertSame($params, $route->params());
    }

    /** @test */
    public function matchRouteClosure(): void
    {
        $route = new Route();
        $route->usingPattern('edit/:id');
        $route->usingMethod(Route::ANY);
        $route->usingAction(fn() => true);

        $this->assertTrue($route->matchTo(Route::GET, 'edit/33')); // valor calculado
        $this->assertEquals([ 'id' => '33' ], $route->params());
    }

    /** @return array<int, array> */
    public function routesNotFoundProvider(): array
    {
        return [
            [ 'edit/:id', "edit"  ], // tamanho diferente
            [ ':id', "edit/33"  ], // tamanho diferente

            [ 'edit/:id', "edit/33/show"  ], // tamanho e padrão diferentes
            [ ':id/edit', "33/edit/show"  ], // tamanho e padrão diferentes

            [ 'edit/:id', "edity/33"  ], // padrão diferente
            [ ':id/edit', "33/edity"  ], // padrão diferente
        ];
    }

    /**
     * @test
     * @dataProvider routesNotFoundProvider
    */
    public function notMatchRoute(string $pattern, string $path): void
    {
        $route = new Route();
        $route->usingPattern($pattern);
        $route->usingMethod(Route::ANY);

        $this->assertFalse($route->matchTo(Route::GET, $path));
    }
}
