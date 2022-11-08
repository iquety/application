<?php

declare(strict_types=1);

namespace Iquety\Application\Http;

class HttpMethod
{
    public const ANY = 'ANY';
    public const DELETE = 'DELETE';
    public const GET = 'GET';
    public const PATCH = 'PATCH';
    public const POST = 'POST';
    public const PUT = 'PUT';

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
