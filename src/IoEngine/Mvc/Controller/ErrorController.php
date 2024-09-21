<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Mvc\Controller;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\Application;
use Throwable;

class ErrorController extends Controller
{
    // error controlles não possui ioc
    public function execute(Throwable $exception): string
    {
        return $exception->getMessage();


        // return $exception->getMessage();

        // $content = sprintf(
        //     "Error: %s on file %s in line %d\n%s",
        //     $exception->getMessage(),
        //     $exception->getFile(),
        //     $exception->getLine(),
        //     $exception->getTraceAsString()
        // );

        // if (in_array($this->mimeType, [HttpMime::JSON, HttpMime::XML]) === true) {
        //     $content = [
        //         'message' => $exception->getMessage(),
        //         'file'    => $exception->getFile(),
        //         'line'    => $exception->getLine(),
        //         'trace'   => $exception->getTrace(),
        //     ];
        // }
    }
}
