# Aplicação

[◂ Como funciona](01-como-funciona.md) | [Índice da documentação](indice.md) | [Inicialização ▸](03-modulo.md)
-- | -- | --

## 1. Conceitos importantes

### 1.1. SOC

blá blá

### 1.2. MVC

blá blá

### 1.3. Ports and Adapters

blá blá

### 1.3. Inversion of Control

blá blá

## 2. Inicializando a aplicação

### 2.1. Implementação

Para criar uma nova aplicação, basta iniciá-la no arquivo principal do projeto.
Geralmente será no arquivo `index.php`:

```php
<?php

declare(strict_types=1);

use Iquety\Application\Application;
use Core\AppBootstrap;
use Modules\Admin\AdminBootstrap;
use Modules\Articles\ArticlesBootstrap;

$app = Application::instance();

// inicializa as configurações da aplicação
$app->bootApplication(new AppBootstrap());

// inicializa as configurações dos módulos que estarão disponíveis
$app->bootModule(new AdminBootstrap());
$app->bootModule(new UserBootstrap());

// executa a aplicação
$response = $app->run();

// emite a resposta ao usuário
$app->sendResponse($response);
```

### 2.2. Application::instance()

Trata-se de um construtor `Singleton`, que torna a aplicação acessível de qualquer lugar do sistema.

### 2.3. bootApplication()

Neste método, especifica-se o objeto contendo as configurações de escopo global da aplicação. As implementações efetuadas estarão disponíveis para todos os módulos em execução.

### 2.4. bootModule()

Neste método, especifica-se o objeto contendo as configurações de um módulo específico. As implementações efetuadas estarão disponíveis apenas quando alguma rota do módulo for acessada.

Isso significa que as dependências configuradas em um módulo só serão fabricadas se uma determinada rota configurada no módulo for acessada.

Isso é poderoso, pois apenas as dependências realmente necessárias estarão na memória do servidor na sessão atualmente em execução.

### 2.5. run()

Este método executa efetivamente a aplicação devolve um objeto do tipo `Psr\Http\Message\ResponseInterface`.

### 2.6. sendResponse()

Este método recebe um objeto do tipo `Psr\Http\Message\ResponseInterface` e libera o seu conteúdo para o cliente, que efetuou a requisição à rota correspondente.

[◂ Como funciona](01-como-funciona.md) | [Índice da documentação](indice.md) | [Inicialização ▸](03-modulo.md)
-- | -- | --
