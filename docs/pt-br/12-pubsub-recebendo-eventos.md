# PubSub: recebendo eventos

[◂ PubSub: enviando eventos](11-pubsub-enviando-eventos.md) | [Índice da documentação](indice.md) | [Arquitetura ▸](98-arquitetura.md)
-- | -- | --

## 1. O que é um Assinante

Um Assinante (Subscriber) é responsável pela manipulação dos eventos ocorridos.
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
    // humm... vamos fabricar o UsuarioCadastrado
    if ($eventLabel === 'usuario-cadastrado') { 
        return UsuarioCadastrado::factory($eventData);
    }

    return null;
}
```

## 3. O método "handleEvent"

Este método recebe a instância de um evento e deve invocar a regra de negócio
adequada para ele. Por exemplo, se for um evento de cadastro, pode invocar algum
repositório ou serviço que efetue o cadastro apropriado.

```php
public function handleEvent(Event $event): void
{
    if ($event instanceof UsuarioCadastrado) {
        // aqui implementamos a rotina que cria
        // um novo usuário no banco de dados

        return;
    }

    if ($event instanceof EmailAlterado) {
        // aqui implementamos a rotina que atualiza o email
        // de um usuário existente no banco de dados
    }
}
```

## 4. O método "subscribedToEventType"

Este método deve retornar o tipo de evento que o Assinante (Subscriber) é capaz
de manipular. Apenas eventos deste tipo serão recebidos no método `handleEvent`.

```php
public function subscribedToEventType(): string
{
    // Apenas eventos deste tipo serão recebidos por este assinante
    return EventoUsuario::class;
}
```

> **Importante:** O tipo de evento pode ser determinado através de polimorfismo.
Por exemplo, se `subscribedToEventType` retornar o tipo `EventoUsuario`, apenas
os eventos que implementarem a interface `EventoUsuario` serão recebidos no
método `handleEvent`. Se retornar o tipo `DomainEvent`, todos os eventos serão
recebidos.

## 5. Exemplo de um Assinante

Abaixo, um exemplo de implementação para o "`UsuarioAssinanteEventos`":

```php
declare(strict_types=1);

namespace Foo\User;

use Foo\User\Events\EmailAlterado;
use Foo\User\Events\UsuarioCadastrado;
use Iquety\PubSub\Event\Event;
use Iquety\PubSub\Subscriber\EventSubscriber;

class UsuarioAssinanteEventos implements EventSubscriber
{
    /** @param array<string,mixed> $eventData */
    public function eventFactory(string $eventLabel, array $eventData): ?Event
    {
        if ($eventLabel === 'usuario-cadastrado') {
            return UsuarioCadastrado::factory($eventData);
        }

        if ($eventLabel === 'usuario-email-alterado') {
            return EmailAlterado::factory($eventData);
        }

        return null;
    }

    public function handleEvent(Event $event): void
    {
        if ($event instanceof UsuarioCadastrado) {
            // aqui implementamos a rotina que cria
            // um novo usuário no banco de dados

            return;
        }

        if ($event instanceof EmailAlterado) {
            // aqui implementamos a rotina que atualiza o email
            // de um usuário existente no banco de dados
        }
    }

    public function subscribedToEventType(): string
    {
        // Apenas eventos deste tipo serão recebidos por este assinante
        return EventoUsuario::class;
    }
}
```

[◂ PubSub: enviando eventos](11-pubsub-enviando-eventos.md) | [Índice da documentação](indice.md) | [Arquitetura ▸](98-arquitetura.md)
-- | -- | --
