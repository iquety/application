# Motores

[◂ O timezone](03-timezone.md) | [Índice da documentação](indice.md) | [Motor MVC ▸](05-motor-mvc.md)
-- | -- | --

Nesta biblioteca, chamamos de **"motor"** a forma como as requisições do usuário
são tratadas pela aplicação. Cada motor implementa um padrão arquitetural diferente.
O mais famoso deles é conhecido como MVC, mas é possível usar outros.

Um uso interessante é a possibilidade de utilizar motores diferentes para cada
necessidade, podendo ser executados ao mesmo tempo.

Por exemplo, é possível usar "MVC" para um faixa de URIs e "FrontController" para
outra faixa.

```php
<?php

declare(strict_types=1);

use Iquety\Application\Application;
use Iquety\Application\IoEngine\FrontController\FcEngine;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Http\Adapter\HttpFactory\DiactorosHttpFactory;

ini_set('display_errors', 1);
error_reporting(E_ALL);

require dirname(__DIR__) . '/vendor/autoload.php';

$app = Application::instance();

$app->bootEngine(new MvcEngine());
$app->bootEngine(new FcEngine());

// registramos a instância do módulo principal
$app->bootApplication(...); 

// registramos as instâncias de um ou mais módulos secundários
$app->bootModule(...);

$request = new DiactorosHttpFactory();

$response = $app->run($request->createRequestFromGlobals());

$app->sendResponse($response);
```

O **Módulo** conterá a implementação necessária para que o motor desejado encontre
os Controladores (Mvc), os Comandos (FrontController) ou as Rotinas (Console).

[◂ O timezone](03-timezone.md) | [Índice da documentação](indice.md) | [Motor MVC ▸](05-motor-mvc.md)
-- | -- | --
