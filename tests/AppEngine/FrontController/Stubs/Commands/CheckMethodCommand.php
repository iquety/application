<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController\Stubs\Commands;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\FrontController\Command\Command;
use Iquety\Application\Http\HttpMethod;

class CheckMethodCommand extends Command
{
    private HttpMethod $httpMethod;

    protected function useMethod(HttpMethod $httpMethod): void
    {
        $this->httpMethod = $httpMethod;
    }

    /** @SuppressWarnings(PHPMD.ShortVariable) */
    public function execute(Input $input, int $id): string
    {
        $this->forMethod($this->httpMethod);

        return sprintf(
            'Resposta %s para id %d input %s',
            $this->httpMethod->value,
            $id,
            $input
        );
    }
}
