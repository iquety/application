# Criando uma aplicação

[◂ Índice da documentação](indice.md) | [Modo de execução ▸](02-modo-de-execucao.md)
-- | --

Toda aplicação web deve ter um ponto de início, ou seja, um arquivo que será
invocado todas as vezes que qualquer requisição for feita para o servidor web.
Geralmente, este arquivo é o `index.php` e se encontra no diretório raiz (docroot)
configurado no servidor. Tecnicamente, podemos dizer que este arquivo é o
`bootstrap` da nossa aplicação.

É neste arquivo que devemos inicializar a biblioteca `Iquety Application` e
configurá-la de acordo com as necessidades da nossa aplicação web.

```php
<?php
// index.php

declare(strict_types=1);

use Iquety\Application\Application;
use Iquety\Application\IoEngine\FrontController\FcEngine;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Http\Adapter\HttpFactory\DiactorosHttpFactory;

ini_set('display_errors', 1);
error_reporting(E_ALL);

require dirname(__DIR__) . '/vendor/autoload.php';

$app = Application::instance();

$app->runIn(Environment::PRODUCTION);

$app->useTimezone(new DateTimeZone('America/Vancouver'));

$app->bootEngine(...); // motor 1
$app->bootEngine(...); // motor 2

$app->bootApplication(...); // módulo principal

$app->bootModule(...); // módulo secundário 1
$app->bootModule(...); // módulo secundário 2

$request = new DiactorosHttpFactory();

$response = $app->run($request->createRequestFromGlobals());

$app->sendResponse($response);
```

[◂ Índice da documentação](indice.md) | [Modo de execução ▸](02-modo-de-execucao.md)
-- | --
