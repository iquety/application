# PubSub: enviando eventos

--page-nav--

## 1. O que é um Evento

Um evento é o encapsulamento de informações que representam uma ação ocorrida em
um determinado momento no tempo. Eventos devem sempre ser nomeados no passado,
pois são alguma coisa que já aconteceu (ex.: UserRegistered, PasswordChanged etc).
As "consequências" de um evento são determinadas pelo assinante (Subscriber),
como será explicado em [PubSub: recebendo eventos](13-pubsub-recebendo-eventos.md).

## 2. Como implementar um Evento

Um novo evento deve cumprir o contrato de `Iquety\Application\PubSub\DomainEvent`.

A implementação mínima deve contemplar os métodos `__constructor` e `label`:

### 2.1. Construtor

Todos os eventos devem receber seus valores somente através do construtor. Não
deve ser possível alterá-los depois da instanciação, a fim de garantir sua
imutabilidade.

> **Importante:** Valores de data devem implementar `DateTimeImmutable`!

```php
public function __construct(
    private string $name,
    private string $cpf,
    private DateTimeImmutable $schedule
) {
}
```

### 2.2. Método label

Este método deve devolver uma **identificação textual única**, que nomeie o evento
de forma clara e objetiva. Deve ser um nome declarativo e facilmente reconhecível
por humanos.

Bons exemplos de identificação são 'user_registered' ou 'user.registered'.

Péssimos exemplos são 'registered', '12345' ou 'abst345sd'.

```php
public function label(): string
{
    return 'user.registered';
}
```

### 2.3. Getters

Getters podem ser implementados, desde que não alterem o estado atual do evento
e funcionem apenas como acessores de dados.

```php
class UserRegistered extends Iquety\Application\PubSub\DomainEvent
{
    public function __construct(
        private string $name,
        private string $cpf,
        private DateTimeImmutable $schedule
    ) {
    }

    public function label(): string
    {
        return 'user.registered';
    }

    public function cpf(): string
    {
        return $this->cpf;
    }

    public function name(): string
    {
        return $this->name;
    }
}
```

### 2.4. Métodos herdados no evento

A classe abstrata `Iquety\Application\PubSub\DomainEvent` fornece quatro métodos
específicos:

#### 2.4.1. O método "factory"

Este método recebe um array associativo contendo os dados do evento (`$values`).
Com base nesses valores, o `factory` deve fabricar o evento devolvê-lo adequadamente
no retorno. Se um valor adicional chamado `occurredOn` for fornecido com uma
instância de `DateTimeImmutable`, a data será aplicada no evento que será criado.

> **Importante:** O valor de retorno sempre deve ser um evento do mesmo tipo,
sendo que a impossibilidade de se fabricar um novo evento deve disparar uma exceção.

> **Mais importante ainda:** Este método pode ser sobrescrito para favorecer
retrocompatibilidades de eventos. Caso a implementação dos valores de um evento
existente precisem mudar, seja por uma evolução no sistema ou por uma correção
necessária, este método deverá garantir o máximo possível de retrocompatibilidade
com os dados implementados em versões anteriores. Isso é necessário para garantir
que módulos ou subsistemas que ainda não se atualizaram, possam continuar enviando
eventos, mesmo que incompletos.

```php
/** @param array<string,mixed> $values */
public static function factory(array $values): Event
{
    // na versão anterior 'cpf' se chamava 'document'
    if (isset($values['document']) === true) {
        $values['cpf'] = $values['document'];
    }

    return parent::factory($values);
}
```

#### 2.4.2. O método "occurredOn"

Este método devolve uma instancia de `DateTimeImmutable`, contendo o valor para
a data e hora atuais, representando o momento quando o evento aconteceu.

```php
public function occurredOn(): DateTimeImmutable;
```

#### 2.4.3. O método "sameEventAs"

Este método compara duas instâncias para determinar se tratam-se do mesmo evento.

```php
/** @param UserRegistered $other */
public function sameEventAs(Event $other): bool;
```

#### 2.4.4. O método "toArray"

Este método devolve um array associativo contendo os valores do evento em tipos
primitivos simples: string, int, float e bool. Além dos argumentos passados no
construtor, este método devolverá um valor adicional chamado 'occurredOn' com o
momento da ocorrência do evento.

```php
public function toArray(): array;
```

### 2.5. Exemplo

Abaixo, um exemplo de implementação para o evento "UserRegistered":

```php
declare(strict_types=1);

namespace Foo\User\Events;

use DateTimeImmutable;
use Iquety\PubSub\Event\Event;

class UserRegistered implements Event
{
    public function __construct(
        private string $name,
        private string $cpf,
        private DateTimeImmutable $ocurredOn
    ) {
    }

    public function label(): string
    {
        return 'user.registered';
    }

    /** @param array<string,mixed> $values */
    public static function factory(array $values): Event
    {
        // na versão anterior 'cpf' se chamava 'document'
        if (isset($values['document']) === true) {
            $values['cpf'] = $values['document'];
        }
        
        return new self(
            $values['name'],
            $values['cpf'],
            new DateTimeImmutable($values['ocurredOn'])
        );
    }

    public function cpf(): string
    {
        return $this->cpf;
    }

    public function name(): string
    {
        return $this->name;
    }
}
```

## 3. How to publish an event

Ambas implementações, tanto o [Command (FcEngine)](06-motor-fc.md) como o
[Controller (MvcEngine)](05-motor-mvc.md) possuem o método `publish` para enviar
os eventos para os [assinantes registrados](13-pubsub-recebendo-eventos.md).

```php
// UserController.php

class UserController extends Controller
{
    public function edit(): ResponseInterface
    {
        $this->publish('receiver-channel', new UserRegistered(...));
    }
}
```

No exemplo acima, o método `edit` do controlador `UserController` publica o evento
`UserRegistered` no canal `'receiver-channel'`.

No momento que o evento é publicado, todos os assinantes serão consultados.
Aqueles que forem capazes de receber o evento, irão utilizá-lo.

--page-nav--
