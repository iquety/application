<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Action;

use Iquety\Application\Application;
use Iquety\PubSub\Event\Event;

trait Publishable
{
    /**
     * Quando o publicador simples é usado, uma mensagem é disparada no
     * momento da publicação.
     * @var array<int,string>
     */
    private array $publishedOutput = [];

    protected function publish(string $channel, Event $event): void
    {
        ob_start();

        Application::instance()->eventPublisherSet()->publish($channel, $event);

        $this->publishedOutput[] = ob_get_clean();
    }

    /** @return array<int,string> */
    protected function published(): array
    {
        return $this->publishedOutput;
    }
}
