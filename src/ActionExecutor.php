<?php

declare(strict_types=1);

namespace Iquety\Application;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Action\MethodNotAllowedException;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Application\Http\HttpStatus;
use Iquety\Application\IoEngine\Action\ActionDescriptor;
use Iquety\Application\IoEngine\Module;
use Iquety\Injection\Container;
use Iquety\Injection\InversionOfControl;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ActionExecutor
{
    public function __construct(
        private Container $container,
        private Module $mainModule
    ) {
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function makeResponseBy(ActionDescriptor $descriptor): ResponseInterface
    {
        /** @var HttpResponseFactory */
        $responseFactory = $this->container->get(HttpResponseFactory::class);

        /** @var Input */
        $input = $this->container->get(Input::class);

        $control = new InversionOfControl($this->container);

        try {
            $rawResponse = $control->resolveTo(
                $descriptor->type(),
                $descriptor->action(),
                $this->onlyIocArguments($input)
            );

            return $responseFactory->response($rawResponse, HttpStatus::OK);
        } catch (MethodNotAllowedException) {
            $action = $this->mainModule->getNotFoundActionClass() . '::execute';

            $rawResponse = $control->resolveTo(
                $this->mainModule->getActionType(),
                $action,
                $this->onlyIocArguments($input)
            );

            return $responseFactory->notFoundResponse($rawResponse);
        } catch (Throwable $exception) {
            $this->container->addSingleton(Throwable::class, $exception);

            $rawResponse = $control->resolveTo(
                $this->mainModule->getActionType(),
                $this->mainModule->getErrorActionClass() . '::execute',
                $this->onlyIocArguments($input)
            );

            return $responseFactory->response($rawResponse, HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /** @return array<string,mixed> */
    private function onlyIocArguments(Input $input): array
    {
        return array_filter(
            $input->toArray(),
            fn($key) => is_numeric($key) === false,
            ARRAY_FILTER_USE_KEY
        );
    }
}
