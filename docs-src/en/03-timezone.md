# The timezone

--page-nav--

The timezone is used to control how time is calculated in the application.
Very useful for various purposes, mainly for storing data in the bank or
send events for implementations of routines that make use of messages.

When a new application is instantiated, the default timezone is **'America/Sao_Paulo'**.
But it is possible to change this through the `useTimezone` method:

```php
$app = Application::instance();

$app->useTimezone(new DateTimeZone('America/Vancouver'));
```

--page-nav--
