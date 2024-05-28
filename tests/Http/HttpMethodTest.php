<?php

declare(strict_types=1);

namespace Tests\Http;

use Iquety\Application\Http\HttpMethod;
use Tests\TestCase;

class HttpMethodTest extends TestCase
{
    /** @test */
    public function allConstants(): void
    {
        $this->assertSame('ANY', HttpMethod::ANY->name);
        $this->assertSame('ANY', HttpMethod::ANY->value);

        $this->assertSame('DELETE', HttpMethod::DELETE->name);
        $this->assertSame('DELETE', HttpMethod::DELETE->value);

        $this->assertSame('GET', HttpMethod::GET->name);
        $this->assertSame('GET', HttpMethod::GET->value);

        $this->assertSame('PATCH', HttpMethod::PATCH->name);
        $this->assertSame('PATCH', HttpMethod::PATCH->value);

        $this->assertSame('POST', HttpMethod::POST->name);
        $this->assertSame('POST', HttpMethod::POST->value);

        $this->assertSame('PUT', HttpMethod::PUT->name);
        $this->assertSame('PUT', HttpMethod::PUT->value);
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function allMethods(): void
    {
        $this->assertSame([
            HttpMethod::ANY,
            HttpMethod::DELETE,
            HttpMethod::GET,
            HttpMethod::PATCH,
            HttpMethod::POST,
            HttpMethod::PUT,
        ], HttpMethod::all());
    }
}
