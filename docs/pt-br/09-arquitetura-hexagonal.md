# Arquitetura Hexagonal

[◂ Implementando módulos](07-modulos.md) | [Índice da documentação](indice.md) | [Dependências existentes ▸](10-dependencias-existentes.md)
-- | -- | --

## 1. Registrando dependências

A biblioteca foi implementada para favorecer o uso de Injeção de Dependências.
O local ideal para injetar todas as dependências é no [bootstrap da aplicação (
no arquivo `index.php`)](01-instanciando.md).

O bom uso de dependências deve seguir o princípio de "programar para
interfaces e não para implementações". Dessa forma, uma boa prática é atrelar
uma implementação ao nome da interface e fabricar a dependência a partir da interface.

```php
class CustomMvcBootstrap extends MvcBootstrap
{
    public function bootDependencies(Container $container): void
    {
        // registro da dependência como Singleton
        $container->addSingleton(
            HttpFactory::class,
            new DiactorosHttpFactory()
        );
    }

    ...
}
```

No exemplo acima a interface `HttpFactory` é usada como identificador da dependência.
Já a classe `DiactorosHttpFactory` é a implementação que será fabricada quando `HttpFactory`
for invocada.

## 2. Usando Injeção de Dependências

Tanto o motor [MVC](05-mecanismo-mvc.md) como o [FrontController](06-mecanismo-fc.md)
possuem a Inversão de Controle nos Controladores e Comandos, respectivamente.
Isso significa que, adicionar um argumento cujo tipo corresponda a uma interface,
registrada em um bootstrap, irá ser resolvido automaticamente e disponibilizado
para uso na execução do Controlador/Comando:

```php
// UserController.php

class UserController extends Controller
{
    public function edit(HttpFactory $factory): ResponseInterface
    {
    }
}
```

No exemplo acima, a implementação para `HttpFactory` será resolvida automaticamente
pela Inversão de Controle e disponibilizada como argumento do método `edit`.

## 3. Invocando manualmente

Assim como a Inversão de Controle, os motores [MVC](05-mecanismo-mvc.md) e
[FrontController](06-mecanismo-fc.md) possibilitam a invocação manual das
dependências. Isso pode ser feito através do método `make`, presente nos
Controladores e nos Comandos, respectivamente:

```php
// UserController.php

class UserController extends Controller
{
    public function edit(): ResponseInterface
    {
        $factory = $this->make(HttpFactory::class);
    }
}
```

No exemplo acima, o método `make` fabrica programáticamente a dependência
`DiactorosHttpFactory` com base na interface `HttpFactory`.

[◂ Implementando módulos](07-modulos.md) | [Índice da documentação](indice.md) | [Dependências existentes ▸](10-dependencias-existentes.md)
-- | -- | --
