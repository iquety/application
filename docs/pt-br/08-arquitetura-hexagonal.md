# Arquitetura Hexagonal

[◂ Motor Console](07-motor-console.md) | [Índice da documentação](indice.md) | [Dependências existentes ▸](10-dependencias-existentes.md)
-- | -- | --

## 1. Introdução

Todo programador deveria ter como principal objetivo a produção de código
desacoplado sempre que possível. Inclusive, um dos princípios mais importantes
da Orientação a Objetos é "Programar para interfaces e não para implementações"
e ele ataca justamente o problema do acoplamento.

Este princípio pode ser seguido com o uso do padrão [Container](https://www.php-fig.org/psr/psr-11/)
para registrar dependências. Assim, elas ficarão disponíveis em todo o sistema.

Ao registrar uma dependência, é uma boa prática usar o nome completo da interface
como identificador:

```php

// registra uma fábrica identificada por MinhaInterface::class
Application::instance()
    ->addFactory(MinhaInterface::class, fn() => new MinhaImplementação());

// fabrica uma instância de MinhaImplementação
$instancia = Application::instance()->make(MinhaInterface::class);
```

Usar fábricas para obter dependências forçará o programador a sempre "pensar
seu código" a partir da interface e não da implementação que ela fornece.

## 2. Registrando no Bootstrap

O local ideal para registrar fábricas é no [bootstrap da aplicação](01-instanciando.md),
que geralmente se encontra no arquivo `index.php`:

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

// registra uma fábrica normal
$app->container()->addFactory('identificacao', 'implementacao');

// registra uma fábrica singleton
$app->container()->addSingleton('identificacao', 'implementacao');

// executa a aplicação, e os componentes poderão fabricar as dependências
$response = $app->run(...);
```

## 3. Registrando nos módulos

Quando a aplicação for implementada para usar módulos, pode ser interessante
registrar fábricas específicas para aquele módulo que desejamos. Isso pode ser
feito registrando-as no método `bootDependencies` da implementação do módulos:

```php
class MeuModuloMvc extends MvcModule
{
    public function bootDependencies(Container $container): void
    {
        // registro da dependência como Singleton
        $container->addSingleton(HttpFactory::class, new DiactorosHttpFactory());
    }

    ...
}
```

## 4. Usando as Inversão de Controle

Tanto o motor [MVC](05-motor-mvc.md) como o [FrontController](06-motor-fc.md)
possuem a `Inversão de Controle` em suas ações (nos Controladores ou Comandos,
respectivamente).

Isso significa que ao adicionar um argumento, em qualquer método de um Controlador
ou Comando, cujo tipo corresponda a uma interface que tenha sido registrada como
identificador em uma fábrica, este argumento irá receber automaticamente a
dependência fabricada como valor:

```php
class MeuControlador extends Controller
{
    public function edit(HttpFactory $factory): ResponseInterface
    {
        // Se HttpFactory::class tiver sido usado como identificador de uma
        // fábrica, o argumento $factory irá possuir automaticamente a
        // dependência devidamente fabricada
    }
}
```

## 5. Fabricando manualmente

Além de possibilitar a `Inversão de Controle`, os motores [MVC](05-motor-mvc.md)
e [FrontController](06-motor-fc.md) possibilitam a fabricação manual das
dependências. Isso pode ser feito através do método `make`, presente nos
Controladores e nos Comandos, respectivamente:

```php
class MeuControlador extends Controller
{
    public function edit(): ResponseInterface
    {
        $factory = $this->make(HttpFactory::class);

        // Se HttpFactory::class tiver sido usado como identificador de uma
        // fábrica, a variável $factory receberá a dependência fabricada
    }
}
```

[◂ Motor Console](07-motor-console.md) | [Índice da documentação](indice.md) | [Dependências existentes ▸](10-dependencias-existentes.md)
-- | -- | --
