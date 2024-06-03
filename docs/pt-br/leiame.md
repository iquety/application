# Iquety Application

![PHP Version](https://img.shields.io/badge/php-%5E8.3-blue)
![License](https://img.shields.io/badge/license-MIT-blue)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/5a911e53f0cc421282d847d323f50203)](https://www.codacy.com/gh/ricardopedias/freep-console/dashboard?utm_source=github.com&utm_medium=referral&utm_content=ricardopedias/freep-console&utm_campaign=Badge_Coverage)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/5a911e53f0cc421282d847d323f50203)](https://www.codacy.com/gh/ricardopedias/freep-console/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ricardopedias/freep-console&amp;utm_campaign=Badge_Grade)

[English](../../readme.md) | [Português](leiame.md)
-- | --

## Sinopse

O **Iquety Application** é uma biblioteca para a criação de aplicações modulares usando
padrões arquiteturais MVC, FrontController e Arquitetura Hexagonal (Ports and Adapters).

```bash
composer require iquety/application
```

### Sobre a Aplicação

- Separação de interesses, usando módulos bootáveis;
- Dependências extremamente flexíveis, usando arquitetura Hexagonal (Ports and Adapters);
- Múltiplos mecanismos para gerir entradas do usuário (MVC ou FrontController).

### Sobre cada Módulo

- Pode possuir o mecanismo mais adequado (MVC ou FrontController);
- Pode definir suas próprias rotas;
- Pode definir suas próprias dependências;
- Suas dependências são fabricadas apenas se uma rota do módulo for acessada;
- A invocação das ações (Controller/Command) é feita usando Inversão de Controle.

Para informações detalhadas, consulte o [Sumário da Documentação](indice.md).

## Características da Biblioteca

- Feito para o PHP 8.3 ou superior;
- Codificado com boas práticas e máxima qualidade;
- Bem documentado e amigável para IDEs;
- Feito com TDD (Test Driven Development);
- Implementado com testes de unidade usando PHPUnit;
- Feito com :heart: &amp; :coffee:.

## Créditos

[Ricardo Pereira Dias](https://www.ricardopedias.com.br)
