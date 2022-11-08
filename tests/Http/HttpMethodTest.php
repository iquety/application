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
        $this->assertSame('ANY', HttpMethod::ANY);
        $this->assertSame('DELETE', HttpMethod::DELETE);
        $this->assertSame('GET', HttpMethod::GET);
        $this->assertSame('PATCH', HttpMethod::PATCH);
        $this->assertSame('POST', HttpMethod::POST);
        $this->assertSame('PUT', HttpMethod::PUT);
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
