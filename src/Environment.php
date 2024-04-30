<?php

declare(strict_types=1);

namespace Iquety\Application;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
enum Environment: string
{
    case PRODUCTION  = 'production';
    case DEVELOPMENT = 'development';
    case TESTING     = 'testing';

    public static function makeBy(string $enviromnent): Environment
    {
        return match ($enviromnent) {
            'production'  => self::PRODUCTION,
            'development' => self::DEVELOPMENT,
            'testint'     => self::TESTING,
            default       => self::PRODUCTION
        };
    }
}
