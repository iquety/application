<?php

declare(strict_types=1);

namespace Iquety\Application\Http;

enum HttpMethod: string
{
    case ANY    = 'ANY';
    case DELETE = 'DELETE';
    case GET    = 'GET';
    case PATCH  = 'PATCH';
    case POST   = 'POST';
    case PUT    = 'PUT';

    /** @return array<int,HttpMethod> */
    public static function all(): array
    {
        return [
            self::ANY,
            self::DELETE,
            self::GET,
            self::PATCH,
            self::POST,
            self::PUT,
        ];
    }
}
