# Execution mode

[◂ Creating an application](01-instantiating.md) | [Documentation index](index.md) | [The timezone ▸](03-timezone.md)
-- | -- | --

## 1. Introduction

The execution mode will provide a way to identify, for different purposes, in
which environment the application is running.

```php
$app = Application::instance();

$app->runIn(Environment::PRODUCTION);
```

## 2. DEVELOPMENT

Used by developers, for unstable version of the software.

- Creativity flows freely in this environment;
- Fast iteration, feedback and learning from mistakes;
- Communication and collaboration.

```php
$app->runIn(Environment::DEVELOPMENT);
```

## 3. PRODUCTION

Used by the end user, for the stable version of the software in daily use.

- Reliability, stability and optimized performance;
- User satisfaction;
- Continuous monitoring;
- Scalability for different user loads.

```php
$app->runIn(Environment::PRODUCTION);
```

## 4. STAGE

After the development phase reaches a stable point, the software goes into
"preparation" mode. This environment has a configuration as close as possible to
the production environment, for a thorough examination of the software's behavior
in a controlled environment.

- Thorough and rigorous testing simulates real-world conditions;
- Quality assurance in accordance with user expectations;
- Version Control to help track changes.

```php
$app->runIn(Environment::STAGE);
```

## 5. TESTING

Used in executing automated tests.

- Unit tests;
- Integration tests;
- E2E tests;
- Behavioral tests

```php
$app->runIn(Environment::TESTING);
```

[◂ Creating an application](01-instantiating.md) | [Documentation index](index.md) | [The timezone ▸](03-timezone.md)
-- | -- | --
