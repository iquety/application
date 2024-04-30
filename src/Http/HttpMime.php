<?php

declare(strict_types=1);

namespace Iquety\Application\Http;

enum HttpMime: string
{
    case HTML = 'text/html';
    case JSON = 'application/json';
    case TEXT = 'text/plain';
    case XML  = 'application/xml';

    public static function makeBy(string $mimeType): HttpMime
    {
        return match ($mimeType) {
            'text/html'        => self::HTML,
            'application/json' => self::JSON,
            'text/plain'       => self::TEXT,
            'application/xml'  => self::XML,
            default            => self::HTML
        };
    }
}
