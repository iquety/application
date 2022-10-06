# Freep Application

![PHP Version](https://img.shields.io/badge/php-%5E8.0-blue)
![License](https://img.shields.io/badge/license-MIT-blue)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/5a911e53f0cc421282d847d323f50203)](https://www.codacy.com/gh/ricardopedias/freep-console/dashboard?utm_source=github.com&utm_medium=referral&utm_content=ricardopedias/freep-console&utm_campaign=Badge_Coverage)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/5a911e53f0cc421282d847d323f50203)](https://www.codacy.com/gh/ricardopedias/freep-console/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ricardopedias/freep-console&amp;utm_campaign=Badge_Grade)

[English](../../readme.md) | [Português](leiame.md)
-- | --

## Sinopse

O **Freep Aplication** é uma biblioteca para a criação de aplicações modulares usando
padrões arquiteturais MVC e Hexagonal (Ports and Adapters).

```bash
composer require ricardopedias/freep-application
```

### Aplicação

* Proporciona a separação de interesses, usando módulos bootáveis;
* Baseada no padrão arquitetural MVC;
* Dependências extremamente flexíveis, usando arquitetura Hexagonal (Ports and Adapters).

### Módulo

- Pode definir suas próprias rotas;
- Pode definir suas próprias dependências;
- Suas dependências são fabricadas apenas se uma rota do módulo for acessada;
- Carrega Controladores e Policies usando o padrão de Inversão de Controle.

Para informações detalhadas, consulte o [Sumário da Documentação](indice.md).

## Características

- Feito para o PHP 8.0 ou superior;
- Codificado com boas práticas e máxima qualidade;
- Bem documentado e amigável para IDEs;
- Feito com TDD (Test Driven Development);
- Implementado com testes de unidade usando PHPUnit;
- Feito com :heart: &amp; :coffee:.

## Créditos

[Ricardo Pereira Dias](https://www.ricardopedias.com.br)
