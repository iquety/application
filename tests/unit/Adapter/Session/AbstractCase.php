<?php

declare(strict_types=1);

namespace Tests\Unit\Adapter\Session;

use Iquety\Application\Http\Session;
use Tests\Unit\TestCase;

abstract class AbstractCase extends TestCase
{
    abstract protected function makeFactory(): Session;

    /** @test */
    public function start(): void
    {
        $session = $this->makeFactory();
        $session->start();

        $this->assertTrue($session->isStarted());
    }

    /** @test */
    public function startWithId(): void
    {
        $session = $this->makeFactory();
        $session->start('aaa');

        $this->assertTrue($session->isStarted());
        $this->assertEquals('aaa', $session->identity());
    }

    /** @test */
    public function name(): void
    {
        $session = $this->makeFactory();
        $session->start();

        $session->setName('yyy');
        $this->assertEquals('yyy', $session->name());
    }

    /** @test */
    public function param(): void
    {
        $session = $this->makeFactory();
        $session->start();

        $session->setParam('aaa', 'bbb');

        $this->assertEquals('bbb', $session->param('aaa'));
        $this->assertEquals('bbb', $session->param('aaa'));

        $this->assertEquals('ddd', $session->param('hhh', 'ddd'));
        $this->assertEquals('ddd', $session->param('hhh', 'ddd'));

        $this->assertNull($session->param('hhh'));
    }

    /** @test */
    public function forget(): void
    {
        $session = $this->makeFactory();
        $session->start();

        $session->setParam('aaa', 'bbb');

        $this->assertEquals('bbb', $session->forget('aaa'));
        $this->assertNull($session->forget('aaa'));
    }

    /** @test */
    public function all(): void
    {
        $session = $this->makeFactory();
        $session->start();

        $session->setParam('aaa', 'bbb');

        $this->assertCount(1, $session->all());
    }

    /** @test */
    public function replace(): void
    {
        $session = $this->makeFactory();
        $session->start();

        $session->replace(['aaa' => 'bbb']);
        $this->assertCount(1, $session->all());

        $session->replace(['aaa' => 'bbb', 'ccc' => 'ddd']);
        $this->assertCount(2, $session->all());
    }

    /** @test */
    public function remove(): void
    {
        $session = $this->makeFactory();
        $session->start();

        $session->setParam('aaa', 'bbb');
        $this->assertEquals('bbb', $session->param('aaa'));

        $session->remove('aaa');
        $this->assertNull($session->param('aaa'));
    }

    /** @test */
    public function clear(): void
    {
        $session = $this->makeFactory();
        $session->start('aaa');
        $this->assertCount(0, $session->all());

        $session->setName('bbb');
        $session->setParam('ccc', 'zzz');

        $this->assertCount(1, $session->all());
        $this->assertEquals('aaa', $session->identity());
        $this->assertEquals('bbb', $session->name());

        $this->assertEquals('zzz', $session->param('ccc'));

        $session->clear();

        // id e nome não mudam após a limpeza
        $this->assertEquals('aaa', $session->identity());
        $this->assertEquals('bbb', $session->name());

        $this->assertNull($session->param('ccc'));
    }

    /** @test */
    public function invalidate(): void
    {
        $session = $this->makeFactory();
        $session->start('aaa');
        $this->assertCount(0, $session->all());

        $session->setName('bbb');
        $session->setParam('ccc', 'zzz');

        $this->assertCount(1, $session->all());
        $this->assertEquals('aaa', $session->identity());
        $this->assertEquals('bbb', $session->name());
        $this->assertEquals('zzz', $session->param('ccc'));

        $session->invalidate();

        // A sessao regenerada tem identificacao diferente
        $this->assertNotEquals('aaa', $session->identity());

        // A sessao regenerada tem o mesmo nome
        $this->assertEquals('bbb', $session->name());

        // A sessao regenerada não possui dados
        $this->assertNull($session->param('ccc'));
        $this->assertCount(0, $session->all());
    }

    /** @test */
    public function has(): void
    {
        $session = $this->makeFactory();
        $session->start();

        $session->setParam('abc', 'monomono');
        $this->assertTrue($session->has('abc'));
    }
}
