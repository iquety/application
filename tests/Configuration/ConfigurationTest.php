<?php

declare(strict_types=1);

namespace Tests\Configuration;

use InvalidArgumentException;
use Iquety\Application\Configuration;
use Tests\TestCase;

class ConfigurationTest extends TestCase
{
    /** @test */
    public function getters(): void
    {
        $config = new Configuration();

        $config->set('TEST', 'xxxx');
        $config->set('db_name', 'yyyy');
        $config->set('EMPTY', '');
        $config->set('integer_one', '123');
        $config->set('integer_two', 123);
        $config->set('float_one', '1.23');
        $config->set('float_two', 1.23);
        $config->set('boolean_one', 'false');
        $config->set('boolean_two', false);

        $this->assertSame('xxxx', $config->get('TEST'));
        $this->assertSame('xxxx', $config->get('test'));
        $this->assertSame('yyyy', $config->get('DB_NAME'));
        $this->assertSame('yyyy', $config->get('db_name'));
        $this->assertSame('', $config->get('EMPTY'));
        $this->assertSame('', $config->get('empty'));
        $this->assertSame('123', $config->get('integer_one'));
        $this->assertSame(123, $config->get('integer_two'));
        $this->assertSame('1.23', $config->get('float_one'));
        $this->assertSame(1.23, $config->get('float_two'));
        $this->assertSame('false', $config->get('boolean_one'));
        $this->assertSame(false, $config->get('boolean_two'));

        $this->assertSame(null, $config->get('not exists'));
        $this->assertSame('default', $config->get('not exists', 'default'));
        $this->assertSame(33, $config->get('not exists', 33));
    }

    /** @test */
    public function loadFrom(): void
    {
        $config = Configuration::loadFrom(__DIR__ . '/Stubs/env');

        $this->assertSame([
            "TEST"        => "xxxx",
            "DB_NAME"     => "yyyy",
            "EMPTY"       => "",
            "NUMERIC_ONE" => 123,
            "NUMERIC_TWO" => 1.23,
            "FALSE_1"     => 0,
            "FALSE_2"     => false,
            "FALSE_3"     => FALSE,
            "TRUE_1"      => 1,
            "TRUE_2"      => true,
            "TRUE_3"      => TRUE,
        ], $config->toArray());
    }

    /** @test */
    public function loadFileNotFound(): void
    {
        $file = __DIR__ . '/env';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("File $file not found");

        Configuration::loadFrom($file);
    }
}
