# Motores

[◂ O timezone](03-timezone.md) | [Índice da documentação](indice.md) | [Motor MVC ▸](05-motor-mvc.md)
-- | -- | --

Nesta biblioteca, chamamos de **"motor"** a forma como as requisições do
usuário são tratadas pela aplicação. Cada motor implementa um padrão
arquitetural diferente e o mais famoso deles é conhecido como MVC.

Esta biblioteca oferece a possibilidade de utilizar motores diferentes para
cada necessidade, podendo ser executados ao mesmo tempo. Por exemplo, é possível
usar MVC para um faixa de URIs e FrontController para outra faixa.

```php
$app = Application::instance();

$app->bootEngine(new MvcEngine());
$app->bootEngine(new FcEngine());

$app->bootApplication(/* Bootstrap */);

$response = $app->run();

$app->sendResponse($response);
```

O **Bootstrap** conterá a implementação necessária para que o motor desejado
encontre os Controladores (Mvc) e os Comandos (FrontController).

[◂ O timezone](03-timezone.md) | [Índice da documentação](indice.md) | [Motor MVC ▸](05-motor-mvc.md)
-- | -- | --
