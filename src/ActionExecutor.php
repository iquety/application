<?php

declare(strict_types=1);

namespace Iquety\Application;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\Action\MethodNotAllowedException;
use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\Bootstrap;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Application\Http\HttpStatus;
use Iquety\Injection\Container;
use Iquety\Injection\InversionOfControl;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ActionExecutor
{
    public function __construct(
        private Container $container,
        private Bootstrap $bootstrap
    ) {
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function makeResponseBy(ActionDescriptor $descriptor, Input $input): ResponseInterface
    {
        /** @var HttpResponseFactory */
        $responseFactory = $this->container->get(HttpResponseFactory::class);

        $control = new InversionOfControl($this->container);

        try {
            $rawResponse = $control->resolveTo(
                $descriptor->type(),
                $descriptor->action(),
                $input->toArray()
            );

            return $responseFactory->response($rawResponse, HttpStatus::OK);
        } catch (MethodNotAllowedException) {
            $action = $this->bootstrap->getNotFoundActionClass()
                . '::execute';

            $rawResponse = $control->resolveTo(
                $this->bootstrap->getActionType(),
                $action,
                $input->toArray()
            );

            return $responseFactory->notFoundResponse($rawResponse);
        } catch (Throwable $exception) {
            $this->container->addSingleton(
                'ErrorResponse',
                $responseFactory->serverErrorResponse($exception)
            );

            $action = $this->bootstrap->getErrorActionClass()
                . '::execute';

            return $control->resolveTo(
                $this->bootstrap->getActionType(),
                $action,
                $input->toArray()
            );
        }
    }
}
