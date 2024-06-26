<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc\Stubs;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\Mvc\Controller\Controller;
use Iquety\Application\Http\HttpMethod;

class CheckMethodController extends Controller
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
