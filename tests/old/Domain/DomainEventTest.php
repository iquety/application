<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use DateTimeImmutable;
use DateTimeZone;
use Iquety\Application\Configuration;
use Tests\Unit\Domain\Support\PostRegistered;
use Tests\Unit\TestCase;

class DomainEventTest extends TestCase
{
    /** @test */
    public function eventFactory(): void
    {
        Configuration::instance()->set('timezone', new DateTimeZone('UTC'));

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // Evento Um
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        $eventOne = new PostRegistered(
            'Meu artigo',
            'Um artigo muito legal',
            new DateTimeImmutable('2022/01/10 10:10:10', Configuration::instance()->get('timezone'))
        );

        $this->assertEquals(
            'Meu artigo',
            $eventOne->toArray()['title']
        );

        $this->assertEquals(
            'Um artigo muito legal',
            $eventOne->toArray()['description']
        );

        $this->assertEquals(
            new DateTimeImmutable('2022/01/10 10:10:10'),
            $eventOne->toArray()['schedule']
        );

        $this->assertArrayHasKey('ocurredOn', $eventOne->toArray());

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // Evento Dois
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        $eventTwo = PostRegistered::factory($eventOne->toArray());

        $this->assertEquals(
            'Meu artigo',
            $eventOne->toArray()['title']
        );

        $this->assertEquals(
            'Um artigo muito legal',
            $eventOne->toArray()['description']
        );

        $this->assertEquals(
            new DateTimeImmutable('2022/01/10 10:10:10'),
            $eventOne->toArray()['schedule']
        );

        $this->assertEquals($eventOne->ocurredOn(), $eventTwo->ocurredOn());

        $this->assertTrue($eventTwo->sameEventAs($eventOne));
    }
}
