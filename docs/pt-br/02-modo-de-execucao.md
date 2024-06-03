# Modo de execução

[◂ Criando uma aplicação](01-instanciando.md) | [Índice da documentação](indice.md) | [O timezone ▸](03-timezone.md)
-- | -- | --

## 1. Introdução

O modo de execução irá fornecer uma maneira de identificar, para os diversos objetivos,
em qual ambiente a aplicação está sendo executada.

```php
$app = Application::instance();

$app->runIn(Environment::PRODUCTION);
```

## 2. DEVELOPMENT

Usado pelos desenvolvedores, para a versão instável do sofware.

- Criatividade flui livremente neste ambiente;
- Iteração rápida, feedbacks e aprendizado com os erros;
- Comunicação e colaboração.

```php
$app->runIn(Environment::DEVELOPMENT);
```

## 3. PRODUCTION

Usado pelo usuário final, para a versão estável do sofware em uso no dia a dia.

- Confiabilidade, estabilidade e desempenho otimizado;
- Satisfação do usuário;
- Monitoramento contínuo;
- Escalabilidade para diferentes cargas de usuários.

```php
$app->runIn(Environment::PRODUCTION);
```

## 4. STAGE

Após a fase de desenvolvimento atingir um ponto estável, o software entre em modo
de "preparação". Este ambiente possui uma configuração mais próxima possível do
ambiente de produção, para um exame minucioso do comportamento do software em um
ambiente controlado.

- Testes completos e rigorosos simulam condições do mundo real;
- Garantia de qualidade conforme as expectativas dos usuários;
- Controle de Versão para ajudar a rastrear as alterações.

```php
$app->runIn(Environment::STAGE);
```

## 5. TESTING

Usado na execução dos testes automatizados.

- Testes de unidade;
- Testes de integração;
- Testes E2E;
- Testes comportamentais

```php
$app->runIn(Environment::TESTING);
```

[◂ Criando uma aplicação](01-instanciando.md) | [Índice da documentação](indice.md) | [O timezone ▸](03-timezone.md)
-- | -- | --
