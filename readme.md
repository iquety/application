# Iquety Application

[![GitHub Release](https://img.shields.io/github/release/iquety/application.svg)](https://github.com/iquety/application/releases/latest)
![PHP Version](https://img.shields.io/badge/php-%5E8.3-blue)
![License](https://img.shields.io/badge/license-MIT-blue)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/6383da378a75457fa11a4e403d7ddd19)](https://app.codacy.com/gh/iquety/application/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_coverage)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/6383da378a75457fa11a4e403d7ddd19)](https://app.codacy.com/gh/iquety/application/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

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
- Multiple mechanisms to manage user inputs (MVC, FrontController or Console);
- Publish/Subscribe pattern for event-based architecture.

### About each Module

- May have the most appropriate mechanism (MVC, FrontController or Console);
- You can define your own routes;
- You can define your own dependencies;
- Its dependencies are only manufactured if a module route is accessed;
- The invocation of web actions (Controller/Command) is done using Inversion of Control.

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
