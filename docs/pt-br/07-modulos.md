# Implementando módulos

[◂ Motor FrontController](06-motor-fc.md) | [Índice da documentação](indice.md) | [Arquitetura Hexagonal ▸](09-arquitetura-hexagonal.md)
-- | -- | --

Além do bootstrap principal, definido com `bootApplication`, é possível
inicializar módulos adicionais usando `bootModule`:

```php
$app = Application::instance();

$app->bootEngine(new MvcEngine());
$app->bootEngine(new FcEngine());

$app->bootApplication(new MainMvcBootstrap());

$app->bootModule(new ModuleOneMvcBootstrap());
$app->bootModule(new ModuleTwoMvcBootstrap());
$app->bootModule(new ModuleThreeFcBootstrap());

$response = $app->run();

$app->sendResponse($response);
```

Separar a aplicação em módulos fornece uma forma poderosa de Separação de Preocupações
(SOC), bem como favorece o uso de Contextos Delimitados como sugere o Domain-Driven Design.

O bootstrap deverá ser implementado com base em um motor previamente inicializado
com `bootEngine`. No caso acima, ambos `MvcEngine` e `FcEngine` foram inicializados,
possibilitando adicionar módulos dos tipos `MvcBootstrap` e `FcBootstrap`.

[◂ Motor FrontController](06-motor-fc.md) | [Índice da documentação](indice.md) | [Arquitetura Hexagonal ▸](09-arquitetura-hexagonal.md)
-- | -- | --
