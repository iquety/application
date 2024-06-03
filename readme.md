# Iquety Application

![PHP Version](https://img.shields.io/badge/php-%5E8.3-blue)
![License](https://img.shields.io/badge/license-MIT-blue)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/294855f31e3d46a895f6e1c994e28d3e)](https://app.codacy.com/gh/iquety/application?utm_source=github.com&utm_medium=referral&utm_content=iquety/application&utm_campaign=Badge_Grade)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/5a911e53f0cc421282d847d323f50203)](https://www.codacy.com/gh/ricardopedias/freep-console/dashboard?utm_source=github.com&utm_medium=referral&utm_content=ricardopedias/freep-console&utm_campaign=Badge_Coverage)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/5a911e53f0cc421282d847d323f50203)](https://www.codacy.com/gh/ricardopedias/freep-console/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ricardopedias/freep-console&amp;utm_campaign=Badge_Grade)

[English](readme.md) | [Português](./docs/pt-br/leiame.md)
-- | --

## Synopsis

**Iquety Application** is a library for creating modular applications using
MVC, FrontController and Hexagonal Architecture (Ports and Adapters) architectural patterns.

```bash
composer require iquety/application
```

### About the Application

- Separation of concerns, using bootable modules;
- Extremely flexible dependencies, using Hexagonal architecture (Ports and Adapters);
- Multiple mechanisms to manage user inputs (MVC or FrontController).

### About each Module

- May have the most appropriate mechanism (MVC or FrontController);
- You can define your own routes;
- You can define your own dependencies;
- Its dependencies are only manufactured if a module route is accessed;
- The invocation of actions (Controller/Command) is done using Inversion of Control.

For detailed information, see [Documentation Summary](docs/en/index.md).

## Library Features

- Made for PHP 8.3 or higher;
- Coded with good practices and maximum quality;
- Well documented and friendly to IDEs;
- Made with TDD (Test Driven Development);
- Implemented with unit tests using PHPUnit;
- Made with :heart: &amp; :coffee:.

## Credits

[Ricardo Pereira Dias](https://www.ricardopedias.com.br)
