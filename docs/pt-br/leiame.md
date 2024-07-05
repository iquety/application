# Iquety Application

[![GitHub Release](https://img.shields.io/github/release/iquety/application.svg)](https://github.com/iquety/application/releases/latest)
![PHP Version](https://img.shields.io/badge/php-%5E8.3-blue)
![License](https://img.shields.io/badge/license-MIT-blue)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/6383da378a75457fa11a4e403d7ddd19)](https://app.codacy.com/gh/iquety/application/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_coverage)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/6383da378a75457fa11a4e403d7ddd19)](https://app.codacy.com/gh/iquety/application/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

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
- Múltiplos mecanismos para gerir entradas do usuário (MVC ou FrontController);
- Padrão Publish/Subscribe para arquitetura baseada em eventos.

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
