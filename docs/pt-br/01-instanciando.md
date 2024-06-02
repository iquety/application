# Criando uma aplicação

[◂ Índice da documentação](indice.md) | [Modo de execução ▸](02-modo-de-execucao.md)
-- | --

Toda aplicação web deve ter um ponto de início, um arquivo que é invocado todas as vezes que qualquer requisição é feita para o servidor web. Geralmente, este arquivo é o `index.php` e se encontra no diretório raiz (docroot) configurado no servidor. Tecnicamente, podemos dizer que este arquivo é o `bootstrap` da nossa aplicação.

É neste arquivo que devemos inicializar a biblioteca Iquety Application e configurar a aplicação de acordo com as necessidades da nossa aplicação web.

```php
// index.php

$app = Application::instance();

$app->runIn(Environment::PRODUCTION);

$app->useTimezone(new DateTimeZone('America/Vancouver'));

$app->bootEngine(/* Motor 1 */);
$app->bootEngine(/* Motor 2 */);

$app->bootApplication(/* Bootstrap Principal */);

$app->bootModule(/* Bootstrap Módulo 1*/);
$app->bootModule(/* Bootstrap Módulo 2*/);

$response = $app->run();

$app->sendResponse($response);
```

[◂ Índice da documentação](indice.md) | [Modo de execução ▸](02-modo-de-execucao.md)
-- | --
