<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Console;

use Iquety\Application\IoEngine\Action\ActionDescriptor;

class ConsoleDescriptor extends ActionDescriptor
{
    private string $output = '';

    private int $status = 0;

    public static function factory(string $bootstrapClass, string $output, int $status): self
    {
        $descriptor = new self(
            Script::class,
            $bootstrapClass,
            '',
            ''
        );

        $descriptor->setOutput($output);

        $descriptor->setStatus($status);

        return $descriptor;
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function withOutput(string $output, int $status): ConsoleDescriptor
    {
        return ConsoleDescriptor::factory($this->module(), $output, $status);
    }

    public function setOutput(string $output): void
    {
        $this->output = $output;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function output(): string
    {
        return $this->output;
    }

    public function status(): int
    {
        return $this->status;
    }
}
