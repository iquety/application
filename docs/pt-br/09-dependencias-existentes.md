# Dependências existentes

[◂ Arquitetura Hexagonal](08-arquitetura-hexagonal.md) | [Índice da documentação](indice.md) | [Padrão Publish/Subscribe ▸](10-pubsub.md)
-- | -- | --

## 1. Dependências fornecidas

Além de possibilitar que o desenvolvedor implemente suas próprias dependências,
é possível usar os adaptadores fornecidos pela própria biblioteca, disponíveis no
namespace `Iquety\Http\Adapter`.

As dependências fornecidas pela biblioteca são implementadas conforme as
[interfaces da PSR](https://www.php-fig.org/) para manter o máximo possível de
interoperabilidade e flexibilidade.

## 2. Mensagens Http

No namespace `Iquety\Http\HttpFactory`, encontram-se adaptadores
para usar implementações da PSR 7 e 17.

Atualmente, são disponibilizadas as 3 implementações mais famosas:

- [Diactoros](https://github.com/laminas/laminas-diactoros);
- [Guzzle](https://github.com/guzzle/psr7);
- [NyHolm](https://github.com/Nyholm/psr7).

## 3. Gerenciamento de Sessões

No namespace `Iquety\Http\Adapter\Session`, encontram-se adaptadores
para gerenciamento de sessões.

- MemorySession (Gerenciador falso para implementar testes);
- [NativeSession](https://github.com/symfony/http-foundation).

[◂ Arquitetura Hexagonal](08-arquitetura-hexagonal.md) | [Índice da documentação](indice.md) | [Padrão Publish/Subscribe ▸](10-pubsub.md)
-- | -- | --
