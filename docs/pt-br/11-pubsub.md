# Padrão Publish/Subscribe

[◂ Dependências existentes](10-dependencias-existentes.md) | [Índice da documentação](indice.md) | [PubSub: enviando eventos ▸](12-pubsub-enviando-eventos.md)
-- | -- | --

## 1. Arquitetura orientada a eventos

A primeira coisa a fazer quando implementamos uma arquitetura orientada a eventos
é ter um meio de verificar ininterruptamente a aplicação, aguardando algum evento
acontecer. Quando um evento é identificado, ele deve ser despachado para as rotinas
ou módulos do sistema que estejam preparados para lidar com aquele tipo de evento.

## 2. A abordagem Publish/Subscribe

Existem diversas formas de implementar uma arquitetura orientada a eventos (APIs
Rest, Observers, Ferramentas de Mensageria, etc).

Esta biblioteca implementa um padrão arquitetônico específico de encaminhamento
de eventos chamado de `Publish/Subscribe` ou `PubSub` (Publicar/Assinar em português).

## 3. Adicionando publicadores

A aplicação está preparada para utilizar o padrão `Publish/Subscribe` fazendo uso
da biblioteca [iquety/pubsub](https://github.com/iquety/pubsub).

Para publicar eventos, é preciso inicializar um ou mais publicadores. No exemplo
a seguir, o `SimpleEventPublisher` será utilizado:

```php
// index.php

$app = Application::instance();

// inicializa um publicador de eventos
$app->bootEventPublisher(SimpleEventPublisher::instance());

$app->bootEngine(new MvcEngine());

$app->bootApplication(new MainMvcBootstrap());

$response = $app->run();
```

Atualmente, os [publicadores disponíveis](https://github.com/iquety/pubsub/tree/main/src/Publisher) são:

| Publicador           | Descrição |
|:--                   | :--       |
| PhpEventPublisher    | Para usar com um [intermediador de mensagens](https://github.com/iquety/pubsub/blob/main/docs/pt-br/03-implementando-no-broker.md) (*uma rotina que executa de forma assíncrona no lado do servidor e fica aguardando eventos via soquete*). Propicia a separação real entre os módulos que se comunicam com o sistema. |
| SimpleEventPublisher | A forma mais simples, que usa um ["Observer"](https://github.com/iquety/pubsub/blob/main/docs/pt-br/02-implementando-no-bootstrap.md) que fica aguardando eventos na própria aplicação. |

[◂ Dependências existentes](10-dependencias-existentes.md) | [Índice da documentação](indice.md) | [PubSub: enviando eventos ▸](12-pubsub-enviando-eventos.md)
-- | -- | --
