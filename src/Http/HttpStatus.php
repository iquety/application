<?php

declare(strict_types=1);

namespace Iquety\Application\Http;

enum HttpStatus: int
{
    case CONTINUE = 100;
    case SWITCHING_PROTOCOLS = 101;
    case PROCESSING = 102;            // RFC2518
    case EARLY_HINTS = 103;           // RFC8297
    case OK = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    case NON_AUTHORITATIVE_INFORMATION = 203;
    case NO_CONTENT = 204;
    case RESET_CONTENT = 205;
    case PARTIAL_CONTENT = 206;
    case MULTI_STATUS = 207;          // RFC4918
    case ALREADY_REPORTED = 208;      // RFC5842
    case IM_USED = 226;               // RFC3229
    case MULTIPLE_CHOICES = 300;
    case MOVED_PERMANENTLY = 301;
    case FOUND = 302;
    case SEE_OTHER = 303;
    case NOT_MODIFIED = 304;
    case USE_PROXY = 305;
    // Depreciado https://www.rfc-editor.org/rfc/rfc2616#section-10.3.7
    // case     SWITCH_PROXY = 306;
    case TEMPORARY_REDIRECT = 307;
    case PERMANENTLY_REDIRECT = 308;  // RFC7238
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case PAYMENT_REQUIRED = 402;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case NOT_ACCEPTABLE = 406;
    case PROXY_AUTHENTICATION_REQUIRED = 407;
    case REQUEST_TIMEOUT = 408;
    case CONFLICT = 409;
    case GONE = 410;
    case LENGTH_REQUIRED = 411;
    case PRECONDITION_FAILED = 412;
    case REQUEST_ENTITY_TOO_LARGE = 413;
    case REQUEST_URI_TOO_LONG = 414;
    case UNSUPPORTED_MEDIA_TYPE = 415;
    case REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    case EXPECTATION_FAILED = 417;
    case I_AM_A_TEAPOT = 418;                                               // RFC2324
    case MISDIRECTED_REQUEST = 421;                                         // RFC7540
    case UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    case LOCKED = 423;                                                      // RFC4918
    case FAILED_DEPENDENCY = 424;                                           // RFC4918
    case TOO_EARLY = 425;                                                   // RFC-ietf-httpbis-replay-04
    case UPGRADE_REQUIRED = 426;                                            // RFC2817
    case PRECONDITION_REQUIRED = 428;                                       // RFC6585
    case TOO_MANY_REQUESTS = 429;                                           // RFC6585
    case REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
    case UNAVAILABLE_FOR_LEGAL_REASONS = 451;                               // RFC7725
    case INTERNAL_SERVER_ERROR = 500;
    case NOT_IMPLEMENTED = 501;
    case BAD_GATEWAY = 502;
    case SERVICE_UNAVAILABLE = 503;
    case GATEWAY_TIMEOUT = 504;
    case VERSION_NOT_SUPPORTED = 505;
    case VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;                        // RFC2295
    case INSUFFICIENT_STORAGE = 507;                                        // RFC4918
    case LOOP_DETECTED = 508;                                               // RFC5842
    case NOT_EXTENDED = 510;                                                // RFC2774
    case NETWORK_AUTHENTICATION_REQUIRED = 511;                             // RFC6585

    public static function reason(int $statusCode): string
    {
        return self::all()[$statusCode] ?? '';
    }

    /** @return array<int,string> */
    public static function all(): array
    {
        /**
         * Tabela de tradução para códigos de Status HTTP.
         * Os códigos são definidos em RFC2616.
         * (last updated 2022-11-02).
         * @var array<int,string>
         * @link https://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
         */
        return [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',            // RFC2518
            103 => 'Early Hints',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',          // RFC4918
            208 => 'Already Reported',      // RFC5842
            226 => 'IM Used',               // RFC3229
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // Depreciado https://www.rfc-editor.org/rfc/rfc2616#section-10.3.7
            // 306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',    // RFC7238
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Content Too Large',                                           // RFC-ietf-httpbis-semantics
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',                                               // RFC2324
            421 => 'Misdirected Request',                                         // RFC7540
            422 => 'Unprocessable Content',                                       // RFC-ietf-httpbis-semantics
            423 => 'Locked',                                                      // RFC4918
            424 => 'Failed Dependency',                                           // RFC4918
            425 => 'Too Early',                                                   // RFC-ietf-httpbis-replay-04
            426 => 'Upgrade Required',                                            // RFC2817
            428 => 'Precondition Required',                                       // RFC6585
            429 => 'Too Many Requests',                                           // RFC6585
            431 => 'Request Header Fields Too Large',                             // RFC6585
            451 => 'Unavailable For Legal Reasons',                               // RFC7725
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',                                     // RFC2295
            507 => 'Insufficient Storage',                                        // RFC4918
            508 => 'Loop Detected',                                               // RFC5842
            510 => 'Not Extended',                                                // RFC2774
            511 => 'Network Authentication Required',                             // RFC6585
        ];
    }
}
