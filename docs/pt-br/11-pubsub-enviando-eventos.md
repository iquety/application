# PubSub: enviando eventos

[◂ Padrão Publish/Subscribe](10-pubsub.md) | [Índice da documentação](indice.md) | [PubSub: recebendo eventos ▸](12-pubsub-recebendo-eventos.md)
-- | -- | --

## 1. O que é um Evento

Um evento é o encapsulamento de informações que representam uma ação ocorrida em
um determinado momento no tempo. Eventos devem ser nomeados sempre no passado,
pois são coisas que já aconteceram (ex.: UsuarioRegistrado, SenhaAlterada etc).
As "consequências" de um evento são determinadas pelo assinante (Subscriber),
como será explicado em [PubSub: recebendo eventos](12-pubsub-recebendo-eventos.md).

## 2. Como implementar um Evento

Um novo evento deve cumprir o contrato de `Iquety\Application\PubSub\DomainEvent`.

A implementação mínima deve contemplar os métodos `__constructor` e `label`:

### 2.1. O Construtor

Todos os eventos devem receber seus valores somente através do construtor.
Outro fator importante é que não deve ser possível alterar valores depois da
instanciação, para garantir a imutabilidade do evento.

> **Importante:** também para garantir a imutabilidade, os valores de data devem
implementar `DateTimeImmutable`!

```php
use Iquety\Application\PubSub\DomainEvent;

class UsuarioCadastrado extends DomainEvent
{
    public function __construct(
        private string $nome,
        private string $cpf,
        private DateTimeImmutable $agendadoEm
    ) {
    }

    ...
}
```

### 2.2. O método label

Este método deve devolver uma **identificação textual única**, que nomeie o evento
de forma clara e objetiva. Deve ser um nome declarativo e facilmente reconhecível
por humanos.

Bons exemplos de identificação são 'user_registered' ou 'user.registered'.

Péssimos exemplos são 'registered', '12345' ou 'abst345sd'.

```php
use Iquety\Application\PubSub\DomainEvent;

class UsuarioCadastrado extends DomainEvent
{
    ...

    public function label(): string
    {
        return 'usuario.cadastrado';
    }
}
```

### 2.3. Usando getters

Getters podem ser implementados, desde que não alterem o estado atual do evento
e funcionem apenas como acessores de dados.

```php
use Iquety\Application\PubSub\DomainEvent;

class UsuarioCadastrado extends DomainEvent
{
    public function __construct(
        private string $nome,
        private string $cpf,
        private DateTimeImmutable $agendadoEm
    ) {
    }

    public function label(): string
    {
        return 'usuario.cadastrado';
    }

    public function cpf(): string
    {
        return $this->cpf;
    }

    public function nome(): string
    {
        return $this->nome;
    }

    public function agendadoEm(): DateTimeImmutable
    {
        return $this->agendadoEm;
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

Este método pode ser sobrescrito para favorecer retrocompatibilidades de eventos.
Caso a implementação dos valores de um evento existente precisem mudar, seja por
uma evolução no sistema ou por uma correção necessária, este método deverá garantir
o máximo possível de retrocompatibilidade com os dados implementados em versões
anteriores. Isso é necessário para garantir que módulos ou subsistemas que ainda
não se atualizaram, possam continuar enviando eventos, mesmo que incompletos.

```php
/** @param array<string,mixed> $values */
public static function factory(array $values): Event
{
    // na versão anterior 'cpf' se chamava 'documento'
    if (isset($values['documento']) === true) {
        $values['cpf'] = $values['documento'];
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

## 3. Como publicar um evento

Os diferentes tipos de ações ([FcEngine/Command](06-motor-fc.md), [MvcEngine/Controller](05-motor-mvc.md) ou [ConsoleEngine/ConsoleRoutine](07-motor-console.md)) possuem o método `publish`
para enviar os eventos para os [assinantes registrados](12-pubsub-recebendo-eventos.md).

```php
// UserController.php

class UsuarioControlador extends Controller
{
    public function editar(): ResponseInterface
    {
        $this->publish('canal-recebedor', new UsuarioCadastrado(...));
    }
}
```

No exemplo acima, o método `UsuarioControlador::editar` publica o evento
`UsuarioCadastrado` no canal `'canal-recebedor'`.

No momento que o evento é publicado, todos os assinantes serão consultados.
Aqueles que forem capazes de receber o evento, irão utilizá-lo.

[◂ Padrão Publish/Subscribe](10-pubsub.md) | [Índice da documentação](indice.md) | [PubSub: recebendo eventos ▸](12-pubsub-recebendo-eventos.md)
-- | -- | --
