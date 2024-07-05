# PubSub: recebendo eventos

[◂ PubSub: enviando eventos](12-pubsub-enviando-eventos.md) | [Índice da documentação](indice.md) | [Evoluindo a biblioteca ▸](99-evoluindo.md)
-- | -- | --

## 1. O que é um Assinante

Um Subscriber (Assinante) é responsável pela manipulação dos eventos ocorridos.
Ele deve conter a rotina responsável pela interpretação de um evento e saber o
que fazer quando um evento daquele tipo acontecer.

Um novo Subscriber deve extender a classe abstrata `Iquety\Application\PubSub\Subscriber`,
que exige três métodos específicos.

## 2. O método "eventFactory"

Este método recebe uma string de identificação (`$eventLabel`) e um array associativo
contendo os dados do evento (`$eventData`). Com base nessas informações o "`eventFactory`"
deve fabricar o evento correto e devolvê-lo adequadamente no retorno. Caso não seja
possível fabricar um evento adequado, null deverá ser retornado:

```php
/** @param array<string,mixed> $eventData */
public function eventFactory(string $eventLabel, array $eventData): ?Event
{
    // humm... vamos fabricar o UserRegistered
    if ($eventLabel === 'user-registered') { 
        return UserRegistered::factory($eventData);
    }

    return null;
}
```

## 3. O método "handleEvent"

Este método recebe a instância de um evento e deve invocar a regra de negócio adequada
para ele. Por exemplo, se for um evento de cadastro, pode invocar algum repositório
ou serviço que efetue o cadastro apropriado.

```php
public function handleEvent(Event $event): void
{
    if ($event instanceof UserRegistered) {
        // ...
        // rotina que cria um novo usuário no banco de dados

        return;
    }

    if ($event instanceof UserEmailChanged) {
        // ...
        // rotina que atualiza o email de um usuário existente no banco de dados
    }
}
```

## 4. O método "subscribedToEventType"

Este método deve retornar o tipo de evento que o Subscriber é capaz de manipular.
Apenas eventos deste tipo serão recebidos no método `handleEvent`.

```php
public function subscribedToEventType(): string
{
    // Apenas eventos deste tipo serão recebidos por este assinante
    return UserEvent::class;
}
```

> **Importante:** O tipo de evento pode ser determinado através de polimorfismo.
Por exemplo, se `subscribedToEventType` retornar o tipo `UserEvent`, apenas os
eventos que implementarem a interface `UserEvent` serão recebidos no método `handleEvent`.

## 5. Exemplo

Abaixo, um exemplo de implementação para o "`UserEventSubscriber`":

```php
declare(strict_types=1);

namespace Foo\User;

use Foo\User\Events\UserEmailChanged;
use Foo\User\Events\UserRegistered;
use Iquety\PubSub\Event\Event;
use Iquety\PubSub\Subscriber\EventSubscriber;

class UserEventSubscriber implements EventSubscriber
{
    /** @param array<string,mixed> $eventData */
    public function eventFactory(string $eventLabel, array $eventData): ?Event
    {
        if ($eventLabel === 'user-registered') {
            return UserRegistered::factory($eventData);
        }

        if ($eventLabel === 'user-email-changed') {
            return UserEmailChanged::factory($eventData);
        }

        return null;
    }

    public function handleEvent(Event $event): void
    {
        if ($event instanceof UserRegistered) {
            // ...
            // rotina que cria um novo usuário no banco de dados

            return;
        }

        if ($event instanceof UserEmailChanged) {
            // ...
            // rotina que atualiza o email de um usuário existente no banco de dados
        }
    }

    public function subscribedToEventType(): string
    {
        // Apenas eventos deste tipo serão recebidos por este assinante
        return UserEvent::class;
    }
}
```

[◂ PubSub: enviando eventos](12-pubsub-enviando-eventos.md) | [Índice da documentação](indice.md) | [Evoluindo a biblioteca ▸](99-evoluindo.md)
-- | -- | --
