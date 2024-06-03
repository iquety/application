# O timezone

[◂ Modo de execução](02-modo-de-execucao.md) | [Índice da documentação](indice.md) | [Motores ▸](04-motores.md)
-- | -- | --

O timezone é usado para controlar a forma como o tempo será calculado na aplicação.
Muito útil para diversos fins, principalmente para armazenar dados no banco ou
enviar aventos para implementações de rotinas que façam uso de mensagens.

Quando uma nova aplicação é instanciada, o timezone padrão é **'America/Sao_Paulo'**.
Mas é possível mudar isso através do método `useTimezone`:

```php
$app = Application::instance();

$app->useTimezone(new DateTimeZone('America/Vancouver'));
```

[◂ Modo de execução](02-modo-de-execucao.md) | [Índice da documentação](indice.md) | [Motores ▸](04-motores.md)
-- | -- | --
