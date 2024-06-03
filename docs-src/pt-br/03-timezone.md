# O timezone

--page-nav--

O timezone é usado para controlar a forma como o tempo será calculado na aplicação.
Muito útil para diversos fins, principalmente para armazenar dados no banco ou
enviar aventos para implementações de rotinas que façam uso de mensagens.

Quando uma nova aplicação é instanciada, o timezone padrão é **'America/Sao_Paulo'**.
Mas é possível mudar isso através do método `useTimezone`:

```php
$app = Application::instance();

$app->useTimezone(new DateTimeZone('America/Vancouver'));
```

--page-nav--
