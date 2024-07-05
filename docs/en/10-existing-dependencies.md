# Existing dependencies

[◂ Hexagonal Architecture](09-hexagonal-architecture.md) | [Documentation index](index.md) | [Publish/Subscribe pattern ▸](11-pubsub.md)
-- | -- | --

## 1. Provided dependencies

In addition to enabling the developer to implement their own dependencies, it is
possible to use the adapters provided by the library itself, available in the
`Iquety\Application\Adapter` namespace.

The dependencies provided by the library are implemented according to the
[PSR interfaces](https://www.php-fig.org/) to maintain as much
interoperability and flexibility.

## 2. Http Messages

In the `Iquety\Application\Adapter\HttpFactory` namespace, there are adapters to
use PSR 7 and 17 implementations.

Currently, the 3 most famous implementations are available:

- [Diactoros](https://github.com/laminas/laminas-diactoros);
- [Guzzle](https://github.com/guzzle/psr7);
- [NyHolm](https://github.com/Nyholm/psr7).

## 3. Session Management

In the `Iquety\Application\Adapter\Session` namespace, there are adapters for
session management.

- MemorySession (Fake manager to implement tests);
- [SynfonyNativeSession](https://github.com/symfony/http-foundation).

[◂ Hexagonal Architecture](09-hexagonal-architecture.md) | [Documentation index](index.md) | [Publish/Subscribe pattern ▸](11-pubsub.md)
-- | -- | --
